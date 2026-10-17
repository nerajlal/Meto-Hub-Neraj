<?php

return [
    // Set false on staging; merchant preferences cannot override this switch.
    'indexable' => env('SEARCH_INDEXABLE', true),
];
