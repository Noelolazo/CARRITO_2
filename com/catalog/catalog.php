<?php

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function AddToCatalog($id_producto, $nombre, $precio, $moneda, $stock) {
    echo "AddToCatalog <br>";
    echo $id_producto . $nombre . $precio . $moneda . $stock;
    $catalog_file = 'xmldb/catalogo.xml';
    $catalogo = GetCatalog($catalog_file);
    
    if (!ExistProduct($catalogo, $id_producto)){
        _ExecuteAddToCatalog($catalogo, $catalog_file, $id_producto, $nombre, $precio, $moneda, $stock);
    } else {
        echo "No ha sido posible agregar el producto";
    };
    
};

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function _ExecuteAddToCatalog ($catalogo, $catalog_file, $id_producto, $nombre, $precio, $moneda, $stock) {    
    $agregar = $catalogo->addChild('producto');
    
    $agregar->addAttribute('id_producto', $id_producto);
    $agregar->addChild('nombre', $nombre);
    $agregar->addChild('stock', $stock);
    
    $precios = $agregar->addChild('precio_item');
    $precios->addChild('precio', $precio);
    $precios->addChild('moneda', $moneda);
    
    $catalogo->asXML($catalog_file);
};

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function SubstractCatalog($id_producto, $restar) {
    $catalogo = GetCatalog('xmldb/catalog.xml');
    $ruta = $catalogo->xpath("/catalogo/producto[@id_producto='$id_producto']");  // Corregido el XPath
    if (!empty($ruta)) {
        $producto = GetProductCart($catalogo, $id_producto);
        $nueva_cantidad = $producto->stock - $restar;
        if ($nueva_cantidad < 0) {
            $nueva_cantidad = 0;
        };
        //echo $nueva_cantidad;
        $ruta1 = $producto->xpath("/carritos/producto[@id_producto='$id_producto']/stock");
        unset($ruta1[0][0]);
        $producto->addChild("stock", $nueva_cantidad);

    };

    $catalogo->asXML('xmldb/carrito.xml');

};

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function ModifStockFromCatalog($id_producto, $stock) {
    $catalogo = GetCatalog('xmldb/catalog.xml');
    $ruta = $catalogo->xpath("/catalogo/producto[@id_producto='$id_producto']");  // Corregido el XPath
    if (!empty($ruta)) {
        $producto = GetProductCart($catalogo, $id_producto);
        if ($stock < 0) {
            $stock = 0;
        };
        //echo $nueva_cantidad;
        $ruta1 = $producto->xpath("/carritos/producto[@id_producto='$id_producto']/stock");
        unset($ruta1[0][0]);
        $producto->addChild("stock", $stock);

    };

    $catalogo->asXML('xmldb/carrito.xml');

};
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function ExistProduct($catalogo, $id_producto) {
    $existe = false;
    foreach ($catalogo->producto as $producto){
        if ($producto['id_producto'] == $id_producto) {
            $existe = true;
            // echo $producto->asXML();
            break;
        };
    };

    return $existe;
};

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function GetCatalog ($catalog_file) {
    if (file_exists($catalog_file)) {
        $catalogo = simplexml_load_file($catalog_file);
        // echo "El fichero existe";
    } else {
        $catalogo = new SimpleXMLElement('<catalogo></catalogo>');
    };
    $catalogo->asXML($catalog_file);
    return $catalogo;
};

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function GetProduct ($id_producto) {
    $catalogo = GetCatalog("xmldb/catalogo.xml");
    foreach ($catalogo->producto as $producto){
        if ($producto['id_producto'] == $id_producto) {
            // echo $producto->asXML();
            return $producto;
        };
    };
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function CheckStock ($id_producto) {
    $stock = 0;
    $producto = GetProduct($id_producto);
    $stock = $producto->stock;
    //echo $stock->asXML();

    return $stock;
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function ModifStock ($id_producto, $nuevo_stock) {
    $verificacion = false;
    $catalogo = GetCatalog("xmldb/catalogo.xml");
    $ruta = $catalogo->xpath("/catalogo/producto[@id_producto='$id_producto']/stock");
    
    $ruta[0][0] = $nuevo_stock;
    if ($ruta[0][0] == $nuevo_stock) {
        echo "Correcto";
        $verificacion = true;

    }
    //echo $stock->asXML();
    $catalogo->asXML("xmldb/catalogo.xml");
    return $verificacion;
}
?>