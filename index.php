<?php
/**
 * Copyright (c) 2013 Puvanenthiran Subbaraj
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *  limitations under the License.
 */

	session_start();
	$homeDir = substr($_SERVER['PHP_SELF'],0, strrpos($_SERVER['PHP_SELF'], '/'));
	$_SESSION['homeDir'] = $homeDir;
?>
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
<html>
   <head>
      <title>PayPal Farmers' Market Demo</title>
      <meta name="apple-mobile-web-app-capable" content="yes">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <link rel="stylesheet" href="./css/index.css">
      <link rel="stylesheet" href="./css/jquery.mobile-1.3.1.min.css" />
      <script src="js/jquery-1.9.1.min.js"></script>
      <script src="js/jquery.mobile-1.3.1.min.js"></script>
      <script src="js/jquery.json-2.4.min.js"></script>
      <script src="js/index.js"></script>
   </head>
   <body class="ui-mobile-viewport ui-overlay-c">
   
      <!--
         Page:HomePage --------------------------------------------------------------------------------------------------------------------------------------------------------------------
         -->	
      <div data-role="page" id="home"  data-url="mainpage" tabindex="0" class="ui-page ui-body-c ui-page-footer-fixed ui-page-active">
         <div data-role="header" id="homeHeader">
            <h1 style="margin-left: auto;margin-right: auto"><span style="color:white;font-size: 1em;text-shadow:none; ">FARMERS' MARKET</span></h1>
         </div>
         <div data-role="footer" class="nav-glyphish-example" data-position="fixed" data-tap-toggle="false">
            <div data-role="navbar" class="nav-glyphish-example" data-grid="b" >
               <ul style="background-image: url('images/farmer/leather.png');background-size: 100% 100%;">
                  <li ><a href="#allshoppage" data-transition="slide"  id="Dashboard" data-icon="custom" style="background:transparent " ><font color="White">Stores</font></a></li>
                  <li><a href="#allcatpage" data-transition="slide"  id="RequestButton" data-icon="custom" style="background:transparent "><font color="White">Categories</font></a></li>
                  <li><a data-transition="slide" href="#cart" onclick="showCart();" id="SendButton" data-icon="custom" style="background:transparent ">My cart</a></li>
               </ul>
            </div>
         </div>
      </div>
	  
      <!--
         Page: cart --------------------------------------------------------------------------------------------------------------------------------------------------------------------
         -->	
      <div data-role="page" id="cart" >
         <div data-role="header" id="allcatHeader">
            <a data-transition="slide" data-direction="reverse" href="#home" data-icon="false" style="background-image:url(images/farmer/leather.png);background-size: 100% 100%;border:1px solid black">Home</a>
            <h1><span style="color:white;font-family: Times New Roman,Georgia,serif;font-size: 1em;text-shadow:none;">My Cart</span></h1>
         </div>
         <div data-role="content" style=" opacity:0.9;filter:alpha(opacity=90);">
            <div data-role="fieldcontain" style="padding:10px 20px;" >
               <ul  data-role="listview" >
                  <li style="text-align:center;  background-color: rgb(203, 110, 61);"> <span  id="conTotal_Cart" style="font-style:normal ; color:white">Order Total: <sup style='font-size:0.6em'>$</sup>0</span> </li>
               </ul>
            </div>
            <div data-corners="true">
               <ul data-role="listview" data-inset="true" data-theme="d"  id="idCart" >
               </ul>
               <style>
                  .ui-btn-corner-all {
                  -moz-border-radius: 				0.4em/*{global-radii-buttons}*/;
                  -webkit-border-radius: 				0.4em/*{global-radii-buttons}*/;
                  border-radius: 						0.4em/*{global-radii-buttons}*/;
                  }
               </style>
			   <div class="ui-disabled" style="float:right;" id="divBtnPay"><a href="#" id="btnPay" data-theme="a" data-inline="true"  data-role="button" onclick="proceedToPay();">Pay</a></div>
            </div>
         </div>
         <div data-role="footer" class="nav-glyphish-example" data-position="fixed" data-tap-toggle="false">
            <div data-role="navbar" class="nav-glyphish-example" data-grid="b" >
               <ul style="background-image: url('images/farmer/leather.png');background-size: 100% 100%;">
                  <li ><a href="#allshoppage" data-transition="slide"  id="Dashboard" data-icon="custom" style="background:transparent " ><font color="White">Stores</font></a></li>
                  <li><a href="#allcatpage" data-transition="slide" id="RequestButton" data-icon="custom" style="background:transparent "><font color="White">Categories</font></a></li>
                  <li><a data-transition="slide" href="#cart" onclick="showCart();" id="SendButton" data-icon="custom" style="background:transparent ">My cart</a></li>
               </ul>
            </div>
         </div>
      </div>
	  
      <!--
         Page: All in one page cat
         --------------------------------------------------------------------------------------------------------------------------------------------------------------------
         -->
      <div data-role="page" id="allcatpage" >
         <div data-role="header" id="allcatHeader">
            <a data-transition="slide" data-direction="reverse" href="#home" data-icon="false" style="background-image:url(images/farmer/leather.png);background-size: 100% 100%;border:1px solid black">Home</a>
            <h1><span style="color:white;font-family: Times New Roman,Georgia,serif;font-size: 1em;text-shadow:none;">Categories</span></h1>
         </div>
         <div data-role="content" style=" opacity:0.9;filter:alpha(opacity=90);">
            <div data-role="fieldcontain" style="padding:10px 20px;" >
               <ul  data-role="listview"  >
                  <li style="text-align:center; background-color: rgb(203, 110, 61);opacity: 0.8;"> <span  id="conTotal1" style="font-style:normal ; color:white">Order Total: <sup style='font-size:0.6em'>$</sup>0</span> </li>
               </ul>
            </div>
            <div data-role="collapsible-set" data-iconpos="right" data-collapsed-icon="arrow-r" data-expanded-icon="arrow-d" data-corners="true" data-theme="a" data-content-theme="c" data-mini="false" id="allcatheads">
            </div>
         </div>
         <div data-role="footer" class="nav-glyphish-example" data-position="fixed" data-tap-toggle="false" >
            <div data-role="navbar" class="nav-glyphish-example" data-grid="b" >
               <ul style="background-image: url('images/farmer/leather.png');background-size: 100% 100%;">
                  <li ><a href="#allshoppage" data-transition="slide" id="Dashboard" data-icon="custom" style="background:transparent " ><font color="White">Stores</font></a></li>
                  <li><a href="#allcatpage" data-transition="slide" id="RequestButton" data-icon="custom" style="background:transparent "><font color="White">Categories</font></a></li>
                  <li><a data-transition="slide" href="#cart" onclick="showCart();" id="SendButton" data-icon="custom" style="background:transparent ">My cart</a></li>
               </ul>
            </div>
         </div>
      </div>
	  
      <!--
         Page: All in one page shop
         --------------------------------------------------------------------------------------------------------------------------------------------------------------------
         -->	
      <div data-role="page" id="allshoppage" >
         <div data-role="header" id="allshopHeader">
            <a data-transition="slide" data-direction="reverse" href="#home" data-icon="false" style="background-image:url(images/farmer/leather.png);background-size: 100% 100%;border:1px solid black">Home</a>
            <h1><span style="color:white;font-family:Times New Roman,Georgia,serif;font-size: 1em;text-shadow:none;">Stores</span></h1>
         </div>
         <div data-role="content" style="opacity:0.9;filter:alpha(opacity=90);">
            <div data-role="fieldcontain" style="padding:10px 20px;" >
               <ul  data-role="listview"  >
                  <li style="text-align:center; background-color: rgb(203, 110, 61);opacity: 0.8;"> <span  id="conTotal" style="font-style:normal ; color:white">Order Total: <sup style='font-size:0.6em'>$</sup>0</span> </li>
               </ul>
            </div>
            <div data-role="collapsible-set" data-iconpos="right" data-collapsed-icon="arrow-r" data-expanded-icon="arrow-d" data-corners="true" data-theme="a" data-content-theme="c" data-mini="false" id="allshopheads">
            </div>
         </div>
         <div data-role="footer" class="nav-glyphish-example" data-position="fixed" data-tap-toggle="false">
            <div data-role="navbar" class="nav-glyphish-example" data-grid="b" >
               <ul style="background-image: url('images/farmer/leather.png');background-size: 100% 100%;">
                  <li ><a href="#allshoppage"  data-transition="slide" id="Dashboard" data-icon="custom" style="background:transparent " ><font color="White">Stores</font></a></li>
                  <li><a href="#allcatpage" data-transition="slide" id="RequestButton" data-icon="custom" style="background:transparent "><font color="White">Categories</font></a></li>
                  <li><a data-transition="slide" href="#cart" onclick="showCart();" id="SendButton" data-icon="custom" style="background:transparent ">My cart</a></li>
               </ul>
            </div>
         </div>
      </div>
   </body>
</html>