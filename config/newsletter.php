<?php

return [

    /*
    | Emails released per batch, and the minutes between batches. Keep batch_size x 60 / interval_minutes
    | under your host's hourly sending limit (50 per 1 minute = 3000/hour).
    */
    'batch_size' => max(1, (int) env('NEWSLETTER_BATCH_SIZE', 50)),

    'batch_interval_minutes' => max(1, (int) env('NEWSLETTER_BATCH_INTERVAL_MINUTES', 1)),

];
