<?php

return [
    'remote-disk' => 's3-podcast-episodes',
    'disk'        => 'local-podcast-markup',
    'shows'       => [
        'dcln' => [
            'importer' => \App\FeedImporters\DCLNImporter::class,
            'series'   => [
                'ep'   => [
                    'title' => 'Exquisite Passion: A Deeper Journey into God’s Eternal Purpose',
                    'path'  => 'exquisite-passion',
                ],
                'gal'  => [
                    'title' => 'Spiritual Graffiti: Galatians in 3D',
                    'path'  => 'spiritual-graffiti',
                ],
                'kgdm' => [
                    'title' => 'Everlasting Domain: Restoring the Kingdom Message',
                    'path'  => 'everlasting-domain',
                ],
                'bp'   => [
                    'title' => 'Beautiful Pursuit: Lessons on Knowing the Lord',
                    'path'  => 'beautiful-pursuit',
                ],
                'rd'   => [
                    'title' => 'Rough Diamonds: The Path to Transformation',
                    'path'  => 'rough-diamonds',
                ],
                'eph'  => [
                    'title' => 'Untraceable Riches: Ephesians in 3D',
                    'path'  => 'untraceable-riches',
                ],
                'atff' => [
                    'title' => 'Atomic Freefall: Becoming Strong in the Broken Places',
                    'path'  => 'atomic-freefall',
                ],
                'dm'   => [
                    'title' => 'Day Moves: 1 Thessalonians in 3D',
                    'path'  => 'day-moves',
                ]
            ]
        ]
    ]
];
