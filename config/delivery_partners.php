<?php

return [
    'dtdc' => [
        'name' => 'DTDC',
        'site_url' => 'https://www.dtdc.com',
        'tracking_url_template' => 'https://www.dtdc.com/track-your-shipment/?awb={tracking_number}',
    ],
    'india_post' => [
        'name' => 'India Post',
        'site_url' => 'https://www.indiapost.gov.in',
        // The official portal requires manual consignment entry; do not invent a tracking API.
        'tracking_url_template' => 'https://www.indiapost.gov.in/',
    ],
];
