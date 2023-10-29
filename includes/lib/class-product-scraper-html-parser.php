<?php
/**
 * Main plugin class file.
 *
 * @package WordPress Plugin Template/Includes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
use voku\helper\HtmlDomParser; 
/**
 * Main plugin class.
 */
class Product_Scraper_HTML_Parser {
	public  $options;
    public  $client;
	

	public function __construct(   ) {
       /* $this->options  = array(
            'login' => get_option( 'wpt_user_text_field' ),
            'password' => get_option( 'wpt_password_field' ),
            "trace"=>1,"exceptions"=>1,
            'location' => get_option( 'wpt_soap_text_field' ),
            'encoding'=>' UTF-8',
       );
       $this->$client = new SoapClient(get_option( 'wpt_wsdl_text_field' ), $this->options);*/
       
		// Load API for generic admin functions.
		/*if ( is_admin() ) {
			$this->admin = new Stekas_Admin_API();
		}*/
        
	} // End __construct ()



    public function get_details($url ) {
        try {
            $curl = curl_init(); 
            // set the URL to reach with a GET HTTP request 
            curl_setopt($curl, CURLOPT_URL, $url); 
            // get the data returned by the cURL request as a string 
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); 
            // make the cURL request follow eventual redirects, 
            // and reach the final page of interest 
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true); 
            // execute the cURL request and 
            // get the HTML of the page as a string 
            $html = curl_exec($curl); 
            // release the cURL resources 
            curl_close($curl);
            
            $htmlDomParser = HtmlDomParser::str_get_html($html);
//https://rudrastyh.com/woocommerce/create-product-programmatically.html

            $productDataLit = array(); 
 
            // retrieve the list of products on the page 
            $productElement = $htmlDomParser->find("div.product-main"); 
            $footerElement = $htmlDomParser->find("div.product-footer"); 
            $name =  $productElement->findOne("h1.product_title")->text; 
            $price = floatval(str_replace(',','.',$productElement->findOne(".woocommerce-Price-amount bdi")->text));
            $short_description = $productElement->findOne("div.product-short-description")->innerHtml;
            $description = $footerElement->findOne("div.woocommerce-Tabs-panel--description")->text;
            $image = $productElement->findOne(".woocommerce-product-gallery__image a")->getAttribute("href"); 
            $short_description="<h5>".$name."</h5>".$short_description;
            $description="<h5>".$name."</h5>".$description;
            $brand = $productElement->findOne(".pwb-single-product-brands a")->text;
            $size = $footerElement->findOne(".woocommerce-product-attributes-item__value")->text;
            //woocommerce-product-attributes

            //$image = $productElement->findOne("img")->getAttribute("src"); 

            
            $productData = array( 
                "description" => $description, 
                "shor_description" => $short_description, 
                "image" => $image, 
                "name" => $name, 
                "price" => $price,
                "brand" => $brand,
                "size" => $size
            ); 

            $attributes = array();

  

           if ($brand)
           {
                $attribute = new WC_Product_Attribute();
                //$attribute->set_id( wc_attribute_taxonomy_id_by_name( 'Gamintojas' ) );
                $attribute->set_name( 'Gamintojas' );
                $attribute->set_options( array( $brand ) );
                $attribute->set_position( 1 );
                $attribute->set_visible( true );
                $attribute->set_variation( false );
                $attributes[] = $attribute;
           }
           if ($size)
           {
                $attribute = new WC_Product_Attribute();
                //$attribute->set_id( wc_attribute_taxonomy_id_by_name( 'Talpa' ) );
                $attribute->set_name( 'Talpa' );
                $attribute->set_options( array( $size ) );
                $attribute->set_position( 2 );
                $attribute->set_visible( true );
                $attribute->set_variation( false );
                $attributes[] = $attribute;
           }
           

            $product = new WC_Product_Simple();

            $product->set_name( $name); // product title

            //$product->set_slug( 'medium-size-wizard-hat-in-new-york' );

            $product->set_regular_price( $price); // in current shop currency

            $product->set_short_description( $short_description);
            if ($description)
             $product->set_description( $description );
            $product->set_status("draft");

            if ($image)
            {
                $img_id=$this->upload_image($image, $name);
                if ($img_id)
                    $product->set_image_id( $img_id );
            }

            //$product->set_attributes( $attributes );

            // let's suppose that our 'Accessories' category has ID = 19 
            //$product->set_category_ids( array( 19 ) );
            // you can also use $product->set_tag_ids() for tags, brands etc

            $product->save();

            wp_redirect( "post.php?action=edit&post=".$product->get_id());



            return $productData;

        } catch (Exception $e) { 
            return 'Error';
            //echo PHP_EOL;
            //echo($client->__getLastRequest());
        }
	}

   private function upload_image( $file, $description ) {
        $file_array  = [ 'name' => wp_basename( $file ), 'tmp_name' => download_url( $file ) ];
     
        // If error storing temporarily, return the error.
        if ( is_wp_error( $file_array['tmp_name'] ) ) {
         return $file_array['tmp_name'];
        }
     
        // Do the validation and storage stuff.
        $id = media_handle_sideload( $file_array, 0, $description );
     
        // If error storing permanently, unlink.
        if ( is_wp_error( $id ) ) {
        
         @unlink( $file_array['tmp_name'] );
            return $id;
        }
        else
            update_post_meta( $id , '_wp_attachment_image_alt', $description );

    
        return $id;
     }



	
}
