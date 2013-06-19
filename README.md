Farmer's Market Application
--------------------------
This is a demo application built to demonstrate usage of ppbox framework (see https://github.com/geekstore/ppbox).

It shows a basic menu of a farmer's market, where user can choose a few items to purchase from various merchants and pay them via a number of payment options provided by the ppbox framework.

Dependencies
--------------------------
* PHP 5.4.3 or later
* curl extension for PHP	


Local deployment Steps
--------------------------
1. Go to document root of your web server
2. Clone this git repository `git clone https://github.com/geekstore/ppbox.git`
3. Clone this git repository `git clone https://github.com/geekstore/farmersmarket.git`
4. Set up MySql DB (See `Set up MySQL DB` section)
5. Now you should be able to run this app by URL : `http://localhost/farmersmarket`

Set up MySQL DB
--------------------------
1. Create `farmer` Database
2. import `farmersmarket\sql\farmer.sql` into farmer database
3. Configure DB in `farmersmarket\config\config.ini` as below
	<pre>
	[db]
	host = localhost
	user = root
	pwd = 
	</pre>

<b>DB Tables</b>
<table>
<tr><td><b>Sl.No</td><td><b>Table Name</td><td><b>Description</td>
<tr><td>1</td><td>Categories</td><td>Contains Category's name and Ids</td>
<tr><td>2</td><td>Items</td><td>Contains Item's name, Id, price, categoryId, shopId</td>
<tr><td>3</td><td>Orders</td><td>Contains Order details</td>
<tr><td>4</td><td>Payments</td><td>Contains Payment staus by order Id</td>
<tr><td>5</td><td>Shops</td><td>Contains Shop's name, Id, api credentials, location</td>
</table>


Set up Payment config	
--------------------------
The Payment config (farmersmarket\payments\payments.ini) is main configuration file for payment functionality. This config provides option to configure to Paypal live or sandbox Api credentials and other general cofig options.


<br><b>[general] section</b>

<pre>
[general]
live = '0'
currency = 'USD'
tax_percent = '10'
pmt_library_url = 'ppbox/rest'
</pre>

*	live - '0' for sandbox and '1' for Production
*	currency - default USD 
*	tax_percent - default 10
*	pmt_library_url - Location of ppbox setup, default 'ppbox/rest'

<br><b>[paypal_email] section</b><br>
To configure api credential settings
<pre>
[paypal_email]
api_username =  'mer102_1362122176_biz_api1.gmail.com'
api_password =  '1362122231'
api_signature =  'AjsbJc8Z7bMatF-pWQPqoJdMdJ4AAf70D6iap-C787dthlUmSAbwoCv-'
api_appid = 'APP-80W284485P519543T'
</pre>
 
<br><b>[email_for_communication] section</b><br>
To set up email configuration to send bill / invoice
<pre>
[email_for_communication]
email_from = 'geekstore77@gmail.com'
</pre>

Note
----
Currently, `Share2Pay` and `Voice Pay` payment functionalities are disabled in this demo app. We will release `Share2Pay` and `Voice Pay` payment functionalities soon.

Disclaimer
--------------------------
We are using Apache 2.0 license for this project for providing the source code only and we do not assume any additional liability. Please be advised that since this framework is capable of handling personal and financial information of your customers, you need to comply with relevant guidelines. Do your groundwork before deploying this in production.	
	
