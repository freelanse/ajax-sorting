<?php

if (!defined('ABSPATH')) {
  exit;
}

use Carbon_Fields\Container;
use Carbon_Fields\Field;

Container::make( 'post_meta', 'Дополнительные поля' )
  ->show_on_post_type('product')

  ->add_tab( 'Информация товара', [
      Field::make( 'text', 'price', 'Цена' )->set_attribute('type', 'number'),
     
        Field::make( 'text', 'name', 'Название' )->set_width(50),
        Field::make( 'text', 'width', 'Ширина' )->set_width(50),
               Field::make( 'text', 'color', 'цвет' )->set_width(50),

  ]);