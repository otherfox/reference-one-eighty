<?php

if( !class_exists( 'DigLabs_Paginator_Info' ) )
{
    class DigLabs_Paginator_Info
    {
        public $total_pages;
        public $current_page;
        public $url_expression;
    }

    class DigLabs_Paginator
    {
        private $info;

        public function __construct( DigLabs_Paginator_Info $info )
        {
            $this->info = $info;
        }

        public function render( )
        {
            echo "HERE";
        }
    }
}
