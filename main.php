<?php

echo "<h1>INIT EXECUTION</h1>";

// $catalogo = GetCatalog("xmldb/catalogo.xml");
// $carrito = GetCart("xmldb/catalogo.xml");

include_once("com/cart/cart.php");
//include_once("com/utils/register.php");
include_once("com/catalog/catalog.php");

// AddToCart(2, 2);
ModifFromCart(3, 2, "=");
// CheckStock(2);
// ModifStock(1, 30);
// AddToCatalog(1, "Nintendo Switch", 300, "EUR", 20);
// AddToCatalog(2, "PlayStation 5", 500, "EUR", 3);
// AddToCatalog(3, "XBOX X", 550, "EUR", 10);
// AddToCatalog(4, "PlayStation 4", 300, "EUR", 50);
// AddToCatalog(5, "Nintendo Switch Lite", 200, "EUR", 5);

// echo $catalogo->asXML();
// echo GetProduct($catalogo, 3)->asXML();

//UserRegister("DNI", "Nombre");
?>