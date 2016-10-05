<?php

return [
  'is_debugging' => 'true',
  'events' => [
    [
      'selector'=> 'h1 a',
      'bindEvent'=> 'click',
      'eventCategory'=> 'Click Event',
      'eventAction'=> 'Clicked',
      'eventLabel'=> '{text}',
    ],
    [
      'selector'=> 'h1.widget-title',
      'bindEvent'=> 'mouseenter',
      'eventCategory'=> 'Mouse hover',
      'eventAction'=> 'hovered',
      'eventLabel'=> '{text}',
    ],
    [
      'selector'=> '.site-info a',
      'bindEvent'=> 'in_view',
      'eventCategory'=> 'Visibility',
      'eventAction'=> 'in_view',
      'eventLabel'=> '{href}',
    ],
  ]
];
