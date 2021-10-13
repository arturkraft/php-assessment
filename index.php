<?php

class Product {
  // Properties
  public $name;
  public $image;
  public $quantity;
  public $price;

  // Methods
  function set_name($name) {
    $this->name = $name;
  }
  function get_name() {
    return $this->name;
  }
  function set_image($image) {
    $this->image = $image;
  }
  function get_image() {
    return $this->image;
  }
  function set_quantity($quantity) {
    $this->quantity = $quantity;
  }
  function get_quantity() {
    return $this->quantity;
  }
  function set_price($price) {
    $this->price = $price;
  }
  function get_price() {
    return $this->price;
  }
}

$url='https://dev-test.hudsonstaging.co.uk/';

// using file() function to get content into array
$lines_array=file($url);

// turn array into one variable
$lines_string=implode('',$lines_array);

// a new DOM object 
$dom = new domDocument('1.0', 'utf-8'); 

// load the HTML into the object 
$dom->loadHTML($lines_string); 

//discard any white space 
$dom->preserveWhiteSpace = false; 

//get all the <p> tags
$ptag = $dom->getElementsByTagName('p');

//get all the <img> tags
$imgtag = $dom->getElementsByTagName('img');

//needed for a variable name
$product_number=0;

//loop through all the <p> elements
for($i=0; $i<count($ptag); $i++){
    if ( $ptag->item($i)->getAttribute('class')=="product-name" ){
        ${'product'.$product_number} = new Product();
        ${'product'.$product_number}->set_name($ptag->item($i)->nodeValue);
    }
    if ( strpos($ptag->item($i)->nodeValue, 'Quantity:') !== false ){
        $quantity = preg_replace('/[^0-9.]+/', '', $ptag->item($i)->nodeValue);
        ${'product'.$product_number}->set_quantity($quantity);
    }
    if ( strpos($ptag->item($i)->nodeValue, 'Price:') !== false ){
        $price = preg_replace('/[^0-9.]+/', '', $ptag->item($i)->nodeValue);
        ${'product'.$product_number}->set_price($price);
        $product_number++;
    }
    
}

//reset the product number
$product_number=0;

//loop through all the <img> elements
for($i=0; $i<count($imgtag); $i++){
    ${'product'.$product_number}->set_image($imgtag->item($i)->getAttribute('src'));
    $product_number++;
}

//start the json
$json=array('[');

//populate json
for($i=0; $i<$product_number; $i++){
    $comma = ($i!=$product_number-1 ? ',' : ''); // fix for a trailing comma

    array_push($json, json_encode(array(
    "product" => ${'product'.$i}->get_name(),
     "metadata" => array(
        "image_url" => ${'product'.$i}->get_image(),
        "quantity" => ${'product'.$i}->get_quantity(),
        "price" => ${'product'.$i}->get_price()
     )
    )).$comma);
}

//close the json
array_push($json,']');

//save to a file
file_put_contents('output.json', $json);
print_r($json);



