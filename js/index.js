var total = 0;
var grandTotal = 0;
var items = new Array();
var tipAmount = 0;
var orderID = "";
var postres;
var taxAmount = 0;
var taxPercent = 10;
var db = "farmer"

/**
 * Load category data while page loading
 */
$(document).ready(function () {
    prefetchData();
});

$(document).on("pageinit", "#cart", function () {
    $('#cart').on("swipeleft swiperight", "li.ui-li", function (event) {
        var listitem = $(this),

            // These are the classnames used for the CSS transition
            dir = event.type === "swipeleft" ? "left" : "right",

            // Check if the browser supports the transform (3D) CSS transition
            transition = $.support.cssTransform3d ? dir : false;

        console.debug(listitem.attr('id'));
        if (!listitem.hasClass("deleteable")) {
            listitem.addClass("deleteable");
            var imageId = "#img" + listitem.attr('id');
            $(imageId).attr("style", "height:30px;width:30px;");
        }
    });
});

$(document).on("pageinit", "#allcatpage", function () {
    $('#allcatpage').on("swipeleft swiperight", "li.ui-li", function (event) {
        var listitem = $(this),

            // These are the classnames used for the CSS transition
            dir = event.type === "swipeleft" ? "left" : "right",

            // Check if the browser supports the transform (3D) CSS transition
            transition = $.support.cssTransform3d ? dir : false;
        console.debug(listitem.attr('id'));
        if (!listitem.hasClass("deleteable")) {
            listitem.addClass("deleteable");
            confirmAndDelete(listitem, transition);
        }
    });
});

$(document).on("pageinit", "#allshoppage", function () {
    $("#allshoppage").on("swipeleft", "li.ui-li", function (event) {
        var listitem = $(this),

            // These are the classnames used for the CSS transition
            dir = event.type === "swipeleft" ? "left" : "right",

            // Check if the browser supports the transform (3D) CSS transition
            transition = $.support.cssTransform3d ? dir : false;
        console.debug("swipeleft -" + listitem.attr('id'));
        if (!listitem.hasClass("deleteable")) {
            listitem.addClass("deleteable");
            confirmAndDelete(listitem, transition);
        }
    });

    $("#allshoppage").on("swiperight", "li.ui-li", function (event) {
        var listitem = $(this),

            // These are the classnames used for the CSS transition
            dir = event.type === "swipeleft" ? "left" : "right",

            // Check if the browser supports the transform (3D) CSS transition
            transition = $.support.cssTransform3d ? dir : false;
        console.debug("swiperight -" + listitem.attr('id'));
        if (listitem.hasClass("deleteable")) {
            listitem.removeClass("deleteable");
            listitem.removeClass("deleteable");
            confirmAndDelete(listitem, transition);
        }
    });
});

/**
 * Pre-fetch all data while loading the page
 */

function prefetchData() {
    getallcatlist();
    getallstoreslist();
    $('#allshopheads').collapsibleset("refresh");
    $('#allcatheads').collapsibleset("refresh");
}


/**
 * Remove the item from cart
 */

function removeFromCart(itemid, itemcost) {
    var thisitem = new Array();
    thisitem[0] = itemid;
    thisitem[1] = 1;
    thisitem[2] = itemcost;

    // Lookup
    var index = -1;
    $.each(items, function (i, item) {
        if (item[0] == thisitem[0]) { // mathing id's
            index = i;
        }
    });

    if (index == -1) { // not found
        console.log("No such item");
    } else { // found at index 'index'
        if (items[index][1] > 1)
            items[index][1] = items[index][1] - 1;
        else if (items[index][1] == 1) {
            items[index][1] = items[index][1] - 1;
            items.splice(index, 1);
        }
        total = total - itemcost;
        total = formatNumber(total, 2);
    }

    $("#conTotal").html("Order Total: <sup style='font-size:0.6em'>$</sup>" + total.toFixed(2));
    $("#conTotal2").html("Order Total: <sup style='font-size:0.6em'>$</sup>" + total.toFixed(2));
    $("#conTotal1").html("Order Total: <sup style='font-size:0.6em'>$</sup>" + total.toFixed(2));
    $("#conTotal_Cart").html("Order Total: <sup style='font-size:0.6em'>$</sup>" + total.toFixed(2));

    console.log("Order becomes : " + $.toJSON(items));

    // Update fields on page
    $('#orderInfo').val($.toJSON(items));
    $('#totalAmount').val(total);
    $('#totalTax').val("1"); // TODO: try removing hardcode
    $('#grandTotal').val("0"); // will be calculated on summary page
	togglePayButton(items.length);
}

/** 
 * add item to cart
 */

function addToCart(itemid, itemcost, itemname, shopId, shopName) {
    total = total + itemcost;

    var thisitem = new Array();
    thisitem[0] = itemid;
    thisitem[1] = 1;
    thisitem[2] = itemcost;
    thisitem[3] = itemname;
    thisitem[4] = shopId;
    thisitem[5] = shopName;
    console.log("This item = " + $.toJSON(thisitem));
    // Lookup
    var index = -1;
    $.each(items, function (i, item) {
        if (item[0] == thisitem[0]) { // mathing id's
            index = i;
        }
    });

    if (index == -1) { // not found
        items.push(thisitem);
        index = items.length - 1;
    } else { // found at index 'index'
        items[index][1] = items[index][1] + 1;
    }

    $("#conTotal").html("Order Total:  <sup style='font-size:0.6em'>$</sup>" + total.toFixed(2));
    $("#conTotal1").html("Order Total:  <sup style='font-size:0.6em'>$</sup>" + total.toFixed(2));
    $("#conTotal2").html("Order Total:  <sup style='font-size:0.6em'>$</sup>" + total.toFixed(2));
    $("#conTotal_Cart").html("Order Total:  <sup style='font-size:0.6em'>$</sup>" + total.toFixed(2));
    console.log("Order becomes : " + $.toJSON(items));

    // Update fields on page
    $('#orderInfo').val($.toJSON(items));
    $('#totalAmount').val(total);
    $('#totalTax').val("1"); // TODO: try removing hardcode
    $('#grandTotal').val("0"); // will be calculated on summary page
	togglePayButton(items.length);
}

/** 
 *  Utility method to format the value in 2 digit decimal
 */

function formatNumber(myNum, numOfDec) {
    var dec = Math.pow(10, numOfDec);
    return Math.round(myNum * dec + 0.1) / dec;
}

/**
 * increment cart item
 */

function inc(con, itemid, itemcost, itemname, shopId, shopName, event) {
    console.log(shopId);
    var qtyId = "#count" + con;
    var value = $(qtyId).attr("count");
    value = parseInt(value) + 1;
    console.log(value + "@" + qtyId);
    if (value > 0) {
        $(qtyId).css("display", "inline");
    }

    $(qtyId).attr("count", value);
    $(qtyId).html(value);
    addToCart(itemid, itemcost, itemname, shopId, shopName);
}

/**
 * decrement cart item
 */

function dec(con, itemid, itemcost, itemname, shopId, shopName, event) {
    event.stopPropagation();
    console.log(con);
    var qtyId = "#count" + con;
    var value = $(qtyId).attr("count");
    value = parseInt(value) - 1;
    if (value < 0)
        value = 0;
    else
        removeFromCart(itemid, itemcost);
    $(qtyId).attr("count", value);
    if (value)
        $(qtyId).html(value);
    else {
        $(qtyId).html("");
        $(qtyId).hide("");
    }
}

/**
* Send cart to server and create order
*/

function sendOrderToServer() {
    var order = {};
    var _items = [];

    console.log("Sending to server...");

    for (var i = 0; i < items.length; i++) {
        console.log("name = " + items[i][3]);
        _items[i] = new Array(items[i][0], items[i][1], items[i][4]); // id, qty, shopId
    }

    order.items = _items;
    console.log("Order = " + JSON.stringify(order));
    sData = 'data=' + JSON.stringify(order);
    console.log("Data = " + sData);

    $.ajax({
        url: './rest/index.php/order/place',
        type: "post",
        data: sData,
        async: false,
        success: function (result) {
            orderID = result;
            console.log("Order ID created = " + orderID);
        },
        error: function (xhr, textStatus, errorThrown) {
            console.log('REST call failed: ' + errorThrown + textStatus);
        }
    });
}

/**
 * Redirect to payment functionality
 */

function proceedToPay() {
    sendOrderToServer();
    var url = "./payments/payments.php?orderid=" + orderID;
    console.log("Redirecting to : " + url);
    window.location = url; // redirect
}

/**
 * get all categories and items list
 */

function getallcatlist() {
    var url_getCatList = './rest/index.php/cat/get/';
    postData(url_getCatList);
    var postres1 = postres;

    $("#allcatheads").html("");
    var i;
    for (i = 0; i < postres1.length; i++) {
        var name = postres1[i].cat_name;
        var id = postres1[i].cat_id;

        var htmlli = "<div data-role='collapsible' data-collapsed='true' ><h3>" + name +
            "</h3><div><ul class='ui-listview' id='" + id +
            "' data-role='listview' data-filter='false' data-inset='false'" +
            " data-theme='c' data-count-theme='e' data-header-theme='c' ></ul></div></div>";

        $("#allcatheads").append(htmlli);
        loadAllItemsbyCat(id, name);
    }
}

/**
 * get all stores and items list
 */

function getallstoreslist() {
    var url_getShopList = './rest/index.php/shop/get/';
    postData(url_getShopList);
    var postres1 = postres;

    $("#allshopheads").html("");
    var i;
    for (i = 0; i < postres1.length; i++) {
        var name = postres1[i].shop_name;
        var id = postres1[i].shop_id;

        var htmlli = "<div data-role='collapsible' data-collapsed='true' ><h3 >" + name +
            "</h3><div><ul class='ui-listview' id='" + id +
            "' data-role='listview' data-filter='false' data-inset='false' data-theme='c'" +
            " data-count-theme='e' data-header-theme='c' ></ul></div></div>";
        $("#allshopheads").append(htmlli);
        loadAllItemsbyShop(id, name);
    }
}

/**
 * Utility method to post data
 */

function postData(target) {
    $.ajax({
        url: target,
        type: "post",
        dataType: 'json',
        async: false,
        cache: false
    })
        .success(function (response) {
        console.debug(response);
        postres = response;
    })
        .error(function (response) {
        console.log(target + " returned error");
        return null;
    });
}

/**
 * private method : load all items by categories
 */

function loadAllItemsbyCat(catId, catName) {
    var url_getItemsByCategory = './rest/index.php/cat/get/' + catId;
    postData(url_getItemsByCategory);
    console.debug(postres);

    $("#" + catId).html("");
    for (i = 0; i < postres.length; i++) {
        var name = postres[i].item_name;
        var id = postres[i].item_id;
        var price = postres[i].item_price;
        var container = "concat" + id;
        var desc = postres[i].item_desc;
        var shopName = postres[i].shop_name;
        var shopId = postres[i].shop_id;

        var htmlli = "<li id='cat" + id + "' onclick=\"confirmAndAdd('cat" + id + "');\" data-icon='false'" +
            " style='font-weight: normal;border: 1px solid rgb(43, 43, 43);padding: 5px;font-size: 0.9em;min-height:0px;'" +
            " class='ui-li ui-li-static ui-btn-up-d' ><img src='images/" + db + "/" +
            id + ".png' style='height: 45px;width: 50px;float: left;margin-right: 10px;position: relative;'>" +
            name + "(<sup style='font-size:0.6em'>$</sup>" + price + ")<span id='count" +
            container + "' class='round' count='0' style='font-size:1.0em;display:none;'>" +
            "</span><span style='float:right; width:12%'><img id='imgcat" + id +
            "' src='images/" + db + "/add_to_shopping_cart.png' style='height:30px;width:30px;padding-top: 5px;padding-bottom:5px;'" +
            " onclick=\"inc('" + container + "','" + id + "'," + price + ",'" + name + "','" +
            shopId + "','" + shopName + "',event);\"></span><br/><br/><p class='ui-li-desc' style='font-size: 10px;'>" +
            shopName + "</p></li>";
        $("#" + catId).append(htmlli);
    }
}

/**
 * private method : load all items by shop
 */

function loadAllItemsbyShop(shopId, shopName) {
    var url_getItemsByShop = './rest/index.php/shop/get/' + shopId;
    postData(url_getItemsByShop);
    console.debug(postres);

    $("#" + shopId).html("");
    for (i = 0; i < postres.length; i++) {
        var name = postres[i].item_name;
        var id = postres[i].item_id;
        var price = postres[i].item_price;
        var container = "con" + id;
        var desc = postres[i].item_desc;
        var categoryName = postres[i].cat_name;
        var shopId = postres[i].shop_id;

        var htmlli = "<li id='" + id + "' onclick=\"confirmAndAdd('" + id + "');\" data-icon='false' style='font-weight: normal;border: 1px solid rgb(43, 43, 43);padding: 5px;font-size: 0.9em;min-height:0px;' class='ui-li ui-li-static ui-btn-up-d'><img src='images/" + db + "/" + id + ".png' style='height: 45px;width: 50px;float: left;margin-right: 10px;position: relative;'>" + name + "(<sup style='font-size:0.6em'>$</sup>" + price + ")<span id='count" + container + "' count='0' class='round' style='font-size:1.0em;display:none;'></span><span style='float:right; width:12%'><img id='img" + id + "' src='images/" + db + "/add_to_shopping_cart.png' style='height:30px;width:30px;padding-top: 5px;padding-bottom:5px;' onclick=\"inc('" + container + "','" + id + "'," + price + ",'" + name + "','" + shopId + "','" + shopName + "',event);\"></span><br/><br/><p class='ui-li-desc' style='font-size: 10px;'>" + categoryName + "</p></li>";

        $("#" + shopId).append(htmlli);
    }
}

/**
 * Function to show the current state of cart
 * It simply reads from global 'items' array and creates a list on the 'cart' page
 */

function showCart() {
    $("#idCart").html(""); // Kill the ghosts

    for (i = 0; i < items.length; i++) {
        var id = items[i][0];
        var name = items[i][3];
        var price = items[i][2];
        var shopId = items[i][5];
        var shopName = items[i][5];
        var qty = items[i][1];

        var html_li = "<li id='li" + id + "' data-icon='false' style='font-weight: normal;border: 1px solid rgb(43, 43, 43);padding: 2.5%;' class='ui-li ui-li-static ui-btn-up-d'>" + name + " ( <sup style='font-size:0.6em'>$</sup>" + price.toFixed(2) + " ) x " + qty + "<span style='float:right; width:20%'><img  id='imgli" + id + "' src='images/minus.png' style='display: none; height:30px;width:30px;' onclick=\"removeFromCart('" + id + "','" + price + "'); showCart();\"" + "/></span><br/><br/><p class='ui-li-desc'>" + shopName + "</p></li>";

        console.log("html = " + html_li);

        $("#idCart").append(html_li);
    }

    //calculate tax item
    taxAmount = (total * taxPercent / 100);
    taxAmount = formatNumber(taxAmount, 2)

    $("#idCart").append("<li id='li" + id + "' data-icon='false' style='font-weight: normal;border: 1px solid rgb(43, 43, 43);padding: 2.5%;' class='ui-li ui-li-static ui-btn-up-d'> Tax : <span style='float:right; width:20%'>  <sup style='font-size:0.6em'>$</sup>" + taxAmount.toFixed(2) + "</span></li>");
    $("#conTotal_Cart").html("Order Total: <sup style='font-size:0.6em'>$</sup>" + total.toFixed(2));

    $.mobile.changePage("#cart", {
        transition: "flip"
    });
    $('#idCart').listview('refresh');
}

/**
 * Display delete button and set onclick functionality
 */

function confirmAndDelete(listitem, transition) {
    var id = "#img" + listitem.attr('id');
    console.debug(id);
    $(id).attr("src", "images/minus.png");
    var onclick = $(id).attr("onclick");
    onclick = "dec" + onclick.substring(3);
    console.debug(onclick);
    $(id).attr("onclick", onclick);
    $("#idshopitemlist").listview('refresh');
}

/**
 * Display add button and set onclick functionality
 */

function confirmAndAdd(listId) {
    if ($("#" + listId).hasClass("deleteable")) {
        $("#" + listId).removeClass("deleteable");
        var id = "#img" + listId;
        console.debug(id);
        $(id).attr("src", "images/" + db + "/add_to_shopping_cart.png");
        var onclick = $(id).attr("onclick");
        onclick = "inc" + onclick.substring(3);
        console.debug(onclick);
        $(id).attr("onclick", onclick);
    }
}

function togglePayButton(cartItemsCnt) {
	if (cartItemsCnt) {
		$('#divBtnPay').addClass('ui-block-a');
		$('#divBtnPay').removeClass('ui-disabled');
	} else {
		$('#divBtnPay').addClass('ui-disabled');
		$('#divBtnPay').removeClass('ui-block-a');
	}
}