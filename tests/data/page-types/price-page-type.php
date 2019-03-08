<?php

class Price_Page_Type extends Papi_Page_Type {

  /**
   * The type meta options.
   *
   * @return array
   */
  public function meta() {
    return [
      'name'        => 'Price page',
      'description' => '',
      'template'    => 'pages/price-page.php'
    ];
  }

  /**
   * Remove default WordPress fields.
   *
   * @return array
   */
  public function remove() {
    return ['commentsdiv', 'commentstatusdiv'];
  }

  /**
   * Register content meta box.
   */
  public function register() {
    $this->box( 'Content', [
        papi_property( [
            'title'    => 'Prices',
            'type'     => 'repeater',
            'slug'     => 'list',
            'settings' => [
                'layout' => 'row',
                'items'  => [
                    papi_property( [
                        'title' => 'Title',
                        'type'  => 'string',
                        'slug'  => 'title',
                    ] ),
                    papi_property( [
                        'title' => 'Description',
                        'type'  => 'string',
                        'slug'  => 'Description',
                    ] ),
                    papi_property( [
                        'title'    => 'Prices',
                        'type'     => 'repeater',
                        'slug'     => 'prices',
                        'settings' => [
                            'layout' => 'row',
                            'items'  => [
                                papi_property( [
                                    'title' => 'Title',
                                    'type'  => 'string',
                                    'slug'  => 'title',
                                ] ),
                                papi_property( [
                                    'title' => 'Price',
                                    'type'  => 'string',
                                    'slug'  => 'price',
                                ] ),
                                papi_property( [
                                    'title' => 'Description',
                                    'type'  => 'string',
                                    'slug'  => 'Description',
                                ] ),
                            ]
                        ]
                    ] )
                ]
            ]
        ] )
    ] );
  }
}
