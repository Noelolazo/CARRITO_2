<?php

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function AddToCart($id_producto, $cantidad) {
    echo "AddToCart <br>";
    echo $id_producto . $cantidad;
    $cart_file = 'xmldb/carrito.xml';
    $catalog_file = 'xmldb/catalogo.xml';

    if (ExistProduct(GetCatalog($catalog_file), $id_producto)){
        _ExecuteAddToCart($cart_file, $id_producto, $cantidad);
    } else {
        echo "No hay suficiente producto";
    };

};

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function _ExecuteAddToCart ($cart_file, $id_producto, $cantidad) {
    $carrito = GetCart($cart_file);
    $producto = GetProduct($id_producto);

    //echo $producto->asXML();
    if ((int)$cantidad > (int)$producto->stock) {
        echo "No hay suficiente producto.";
    } else {
        $agregar = $carrito->addChild('producto');
        $agregar->addAttribute('id_producto', $id_producto);
        $agregar->addChild('nombre', $producto->nombre);
        $agregar->addChild('precio_item', "");
        // foreach ($producto->children() as $dato) {
        //     // echo $dato;
        //     if ((string)$dato->getName() == "stock") {
        //         echo "";
        //     } else {
        //         $agregar->addChild($dato->getName(), $dato);
        //     };
        // };
        // $carrito->asXML('xmldb/prueba.xml');
        $precios = $agregar->precio_item;
        foreach ($producto->precio_item->children() as $precio) {
            $precios->addChild($precio->getName(), $precio);
        };
        $precios->addChild("cantidad", $cantidad);
        $precios->addChild("total", (($producto->precio_item->precio)*$cantidad));
    };

    // $agregar->addAttribute('id_producto', $id_producto);
    // $agregar->addChild('id_producto', $id_producto);
    // $agregar->addChild('cantidad', $cantidad);

    // $precios = $agregar->addChild('precio_item');
    // $precios->addChild('precio', $precio);
    // $precios->addChild('moneda', $moneda);

    $carrito->asXML($cart_file);
};

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function RemoveFromCart($id_producto) {
    $carrito = GetCart('xmldb/carrito.xml');
    $ruta = $carrito->xpath("/carritos/producto[@id_producto='$id_producto']");  // Corregido el XPath
    if (!empty($ruta)) {
        unset($ruta[0][0]);

    };

    $carrito->asXML('xmldb/carrito.xml');

};

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function ModifFromCart($id_producto, $cantidad, $realizar) {
    $stock = CheckStock($id_producto);
    $carrito = GetCart('xmldb/carrito.xml');
    $ruta = $carrito->xpath("/carritos/producto[@id_producto='$id_producto']");  // Corregido el XPath
    if (!empty($ruta)) {
        $producto = GetProductCart($carrito, $id_producto);
        switch ($realizar) {
            case "+":
                $nueva_cantidad = $producto->precio_item->cantidad + $cantidad;
                $stock_restante = $stock - $cantidad;
                break;
            case "-":
                $nueva_cantidad = $producto->precio_item->cantidad - $cantidad;
                $stock_restante = $stock + $cantidad;
                break;
            case "=":
                $nueva_cantidad = $cantidad;
                if ($cantidad > $producto->precio_item->cantidad) {
                    $diferencia = $cantidad - $producto->precio_item->cantidad;
                    $stock_restante = $stock - $diferencia;
                    break;
                } else if ($cantidad < $producto->precio_item->cantidad){
                    $diferencia = $producto->precio_item->cantidad - $cantidad;
                    $stock_restante = $stock + $diferencia;
                    break;
                } else {
                    $stock_restante = $stock;
                    break;
                }

            default:
                echo "ERROR: No se ha podido realizar la nueva asignación.";
                return;
                    
            }
        if ($nueva_cantidad > $stock) {
            echo "La cantidad deseada excede el stock disponible";
            return;
        }
        ModifStock($id_producto, $stock_restante);
        //echo "entra 2";
        //echo $nueva_cantidad;
        $ruta1 = $carrito->xpath("/carritos/producto[@id_producto='$id_producto']/precio_item/cantidad");
        $ruta2 = $carrito->xpath("/carritos/producto[@id_producto='$id_producto']/precio_item/total");
        $ruta1[0][0] = $nueva_cantidad;
        $ruta2[0][0] = (($producto->precio_item->precio)*$nueva_cantidad);
    }

    $carrito->asXML('xmldb/carrito.xml');

};
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function GetCart ($cart_file) {
    if (file_exists($cart_file)) {
        $carrito = simplexml_load_file($cart_file);
        // echo "Funciona";
    } else {
        $carrito = new SimpleXMLElement('<carritos></carritos>');
    };
    $carrito->asXML($cart_file);
    return $carrito;
};

function GetProductCart ($carrito, $id_producto) {
    foreach ($carrito->producto as $producto){
        if ($producto['id_producto'] == $id_producto) {
            // echo $producto->asXML();
            return $producto;
        };
    };
}
?>