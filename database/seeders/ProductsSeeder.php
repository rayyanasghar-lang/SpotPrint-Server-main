<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductsSeeder extends Seeder
{
    public function run()
    {
        // Custom function to merge arrays recursively with distinct keys
        function array_merge_recursive_distinct(array &$array1, array &$array2)
        {
            $merged = $array1;

            foreach ($array2 as $key => &$value) {
                $merged[$key] = $array2[$key];
            }

            return $merged;
        }

        // 3% extra chrges for carbon offset of your order // except banners
        $productOptions = [
            // 1. Booklets (Stitched)
            'Booklets' => [
                'formula' => 'Booklets',
                'tax' => ['enabled' => false, 'rate' => '0', 'type' => 'fixed'], // 20% VAT
                'carbon_offset' => [
                    "title" => "Carbon Offset",
                    "description" => "3% extra charges for carbon offset of your order",
                    "options" => [
                        ["title" => "No", "value" => "No", "price_increase" => 0],
                        ["title" => "Yes", "value" => "Yes", "price_increase" => 3],
                    ]
                ],
                'binding' => [
                    "title" => "Binding",
                    "options" => [
                        ["title" => "Stitched", "value" => "Stitched", "image" => "Stitched.png", 'cover' => 'optional', 'required_spin_image' => 'no'],
                        ["title" => "PUR", "value" => "PUR", "image" => "PurBinding.png", 'cover' => 'required', 'required_spin_image' => 'yes']
                    ]
                ],
                'orientation' => [
                    "title" => "Paper Orientation",
                    "options" => [
                        ["title" => "Portrait", "value" => "Portrait"],
                        ["title" => "Landscape", "value" => "Landscape"],
                    ]
                ],
                'paper_sizes' => [
                    "title" => "Paper Size",
                    "options" => [
                        ["title" => "A5 (210 x 148mm)", "value" => "A5"],
                        ["title" => "A4 (297 x 210mm)", "value" => "A4"],
                        ["title" => "B5 (240 x 170mm)", "value" => "B5"],
                        ["title" => "DL (210 x 99mm)", "value" => "DL"],
                        ["title" => "210 x 210mm", "value" => "210"]
                    ]
                ],
                'paper_types' => [
                    "title" => "Paper Type",
                    "options" => [
                        ["title" => "Gloss", "value" => "Gloss", "image" => "GlossPaper.png"],
                        ["title" => "Silk", "value" => "Silk", "image" => "SilkPaper.png"],
                        ["title" => "Uncoated", "value" => "Uncoated", "image" => "UncoatedPaper.png"],
                        ["title" => "Recycled Silk", "value" => "Recycled Silk", "image" => "RecycledSilkPaper.png"],
                        ["title" => "Recycled Uncoated", "value" => "Recycled Uncoated", "image" => "RecycledUncoatedPaper.png"]
                    ]
                ],
                'paper_weights' => [
                    "title" => "Paper Weight",
                    "options" => [
                        'Gloss' => [
                            ["title" => "130 gsm", "value" => "130"],
                            ["title" => "150 gsm", "value" => "150"],
                            ["title" => "170 gsm", "value" => "170"]
                        ],
                        "Silk" => [
                            ["title" => "130 gsm", "value" => "130"],
                            ["title" => "150 gsm", "value" => "150"],
                            ["title" => "170 gsm", "value" => "170"],
                            ["title" => "250 gsm", "value" => "250"],
                            ["title" => "350 gsm", "value" => "350"]
                        ],
                        "Uncoated" => [
                            ["title" => "100 gsm", "value" => "100"],
                            ["title" => "120 gsm", "value" => "120"],
                            ["title" => "150 gsm", "value" => "150"],
                            ["title" => "190 gsm", "value" => "190"],
                            ["title" => "250 gsm", "value" => "250"],
                            ["title" => "300 gsm", "value" => "300"],
                            ["title" => "350 gsm", "value" => "350"]
                        ],
                        "Recycled Silk" => [
                            ["title" => "130 gsm", "value" => "130"],
                            ["title" => "150 gsm", "value" => "150"],
                            ["title" => "170 gsm", "value" => "170"]
                        ],
                        "Recycled Uncoated" => [
                            ["title" => "120 gsm", "value" => "120"],
                            ["title" => "350 gsm", "value" => "350"]
                        ]
                    ]
                ],
                'pages' => [
                    "title" => "Pages",
                    "options" => [
                        'Stitched' => [
                            ["start" => "8", "end" => "48", "step" => "4"]
                        ],
                        'PUR' => [
                            ["start" => "16", "end" => "876", "step" => "2"]
                        ],
                    ]
                ],
                'cover_types' => [
                    "title" => "Cover Type",
                    "options" => [
                        ["title" => "Gloss", "value" => "Gloss", "image" => "GlossHardCover.png"],
                        ["title" => "Silk", "value" => "Silk", "image" => "SilkHardCover.png"],
                        ["title" => "Uncoated", "value" => "Uncoated", "image" => "UncoatedHardCover.png"]
                    ]
                ],
                'cover_weights' => [
                    "title" => "Cover Weights",
                    "options" => [
                        'Gloss' => [
                            ["title" => "250 gsm", "value" => "250", "image" => ""],
                            ["title" => "300 gsm", "value" => "300", "image" => ""]
                        ],
                        "Silk" => [
                            ["title" => "250 gsm", "value" => "250", "image" => ""],
                            ["title" => "300 gsm", "value" => "300", "image" => ""]
                        ],
                        "Uncoated" => [
                            ["title" => "350 gsm", "value" => "350", "image" => ""]
                        ],
                    ]
                ],
                'lamination' => [
                    "title" => "Lamination",
                    "options" => [
                        ["title" => "Matt", "value" => "Matt", "image" => "MattLamination.png"],
                        ["title" => "Gloss", "value" => "Gloss", "image" => "GlossLamination.png"],
                        ["title" => "Soft Touch", "value" => "Soft Touch", "image" => "SoftTouchLamination.png"],
                        ["title" => "Anti Scuff Matt", "value" => "Anti Scuff Matt", "image" => "AntiScuffMattLamination.png"]
                    ]
                ],
                'cover_special_options' => [
                    'foiling_required' => [
                        'title' => 'Foiling Required',
                        'options' => [
                            ['title' => 'No', 'value' => 'No'],
                            ['title' => 'Yes', 'value' => 'Yes'],
                        ],
                        'foil_colours' => [
                            'title' => 'Choose foil colour',
                            'options' => [
                                ['title' => 'Gold', 'value' => 'Gold'],
                                ['title' => 'Silver', 'value' => 'Silver'],
                                ['title' => 'Red', 'value' => 'Red']
                            ]
                        ]
                    ],
                    'embossing_required' => [
                        'title' => 'Embossing Required',
                        'options' => [
                            ['title' => 'No', 'value' => 'No'],
                            ['title' => 'Yes', 'value' => 'Yes'],
                        ]
                    ]
                ],
                'quantity_limits' => [
                    'min' => 50,
                    'max' => 5000
                ],
                'printing_options' => [
                    'allowed_methods' => ['Litho', 'Digital']
                ],
            ],

            'Paperback books' => [
                'binding' => [
                    "title" => "Binding",
                    "options" => [
                        ["title" => "PUR", "value" => "PUR", "image" => "PurBinding.png", 'cover' => 'required', 'required_spin_image' => 'yes']
                    ]
                ],
                'pages' => [
                    "title" => "Pages",
                    "options" => [
                        'PUR' => [
                            ["start" => "16", "end" => "876", "step" => "4"]
                        ],
                    ]
                ],
            ],

            // 2. Flyers (FLAT only)
            'Flyers' => [
                'formula' => 'Flyers',
                'tax' => ['enabled' => false, 'rate' => '0', 'type' => 'fixed'],
                'carbon_offset' => [
                    "title" => "Carbon Offset",
                    "description" => "3% extra charges for carbon offset of your order",
                    "options" => [
                        ["title" => "No", "value" => "No", "price_increase" => 0],
                        ["title" => "Yes", "value" => "Yes", "price_increase" => 3],
                    ]
                ],
                'flyer_types' => [
                    "title" => "Flyer Types",
                    "options" => [
                        ["title" => "FLAT", "value" => "FLAT"]
                    ]
                ],
                'printed_sides' => [
                    "title" => "Printed Sides",
                    "options" => [
                        ["title" => "Single Side", "value" => "Single Side"],
                        ["title" => "Both Sides", "value" => "Both Sides"]
                    ]
                ],
                'paper_sizes' => [
                    "title" => "Paper Size",
                    "options" => [
                        ["title" => "A4 (297 x 210mm)", "value" => "A4"],
                        ["title" => "A5 (210 x 148mm)", "value" => "A5"],
                        ["title" => "A3 (420 x 297mm)", "value" => "A3"],
                        ["title" => "DL (210 x 99mm)", "value" => "DL"],
                        ["title" => "A6 (148 x 105mm)", "value" => "A6"]
                    ]
                ],
                'paper_types' => [
                    "title" => "Paper Type",
                    "options" => [
                        ["title" => "Gloss", "value" => "Gloss", "image" => "GlossPaper.png"],
                        ["title" => "Silk", "value" => "Silk", "image" => "SilkPaper.png"],
                        ["title" => "Uncoated", "value" => "Uncoated", "image" => "UncoatedPaper.png"],
                    ]
                ],
                'paper_weights' => [
                    "title" => "Paper Weight",
                    "options" => [
                        'Gloss' => [
                            ["title" => "115 gsm", "value" => "115"],
                            ["title" => "130 gsm", "value" => "130"],
                            ["title" => "170 gsm", "value" => "170"],
                            ["title" => "200 gsm", "value" => "200"],
                            ["title" => "250 gsm", "value" => "250"],
                            ["title" => "300 gsm", "value" => "300"],
                            ["title" => "350 gsm", "value" => "350"],
                            ["title" => "400 gsm", "value" => "400"],
                        ],
                        "Silk" => [
                            ["title" => "115 gsm", "value" => "115"],
                            ["title" => "130 gsm", "value" => "130"],
                            ["title" => "150 gsm", "value" => "150"],
                            ["title" => "170 gsm", "value" => "170"],
                            ["title" => "200 gsm", "value" => "200"],
                            ["title" => "250 gsm", "value" => "250"],
                            ["title" => "300 gsm", "value" => "300"],
                            ["title" => "350 gsm", "value" => "350"],
                            ["title" => "400 gsm", "value" => "400"],
                        ],
                        "Uncoated" => [
                            ["title" => "120 gsm", "value" => "120"],
                            ["title" => "150 gsm", "value" => "150"],
                            ["title" => "170 gsm", "value" => "170"],
                            ["title" => "250 gsm", "value" => "250"],
                            ["title" => "300 gsm", "value" => "300"],
                            ["title" => "350 gsm", "value" => "350"]
                        ],
                    ]
                ],
                'quantity_limits' => [
                    'min' => 100,
                    'max' => 50000
                ],
                'printing_options' => [
                    'allowed_methods' => ['Litho', 'Digital']
                ],
            ],

            // New product: Folded Flyers
            'Folded Flyers' => [
                'flyer_types' => [
                    "title" => "Flyer Types",
                    "options" => [
                        ["title" => "Folded", "value" => "Folded"]
                    ]
                ],
                'folding_types' => [
                    "title" => "Folding Option",
                    "options" => [
                        ["title" => "Roll Folded", "value" => "Roll Folded", "image" => "RollFolded.png"],
                        ["title" => "Z Fold (Concertina)", "value" => "Z Fold (Concertina)", "image" => "ZFold.png"],
                        ["title" => "Half Folded", "value" => "Half Folded", "image" => "HalfFolded.png"],
                        ["title" => "Other (Will be confirmed prior to print)", "value" => "Other", "image" => "OtherFolded.png"]
                    ]
                ],
            ],

            // Takeaway Menus (inherits from Flyers)
            'Takeaway Menus' => [
                'flyer_types' => [
                    "title" => "Flyer Types",
                    "options" => [
                        ["title" => "Folded", "value" => "Folded"]
                    ]
                ],
            ],

            // 3. Posters
            'Posters' => [
                'formula' => 'Posters',
                'tax' => ['enabled' => true, 'rate' => '20', 'type' => 'percentage'], // 20% VAT
                'carbon_offset' => [
                    "title" => "Carbon Offset",
                    "description" => "3% extra charges for carbon offset of your order",
                    "options" => [
                        ["title" => "No", "value" => "No", "price_increase" => 0],
                        ["title" => "Yes", "value" => "Yes", "price_increase" => 3],
                    ]
                ],
                'paper_sizes' => [
                    "title" => "Paper Size",
                    "options" => [
                        ["title" => "A1 (594 x 840mm)", "value" => "A1"],
                        ["title" => "A2 (594 x 420mm)", "value" => "A2"],
                        ["title" => "A3 (420 x 297mm)", "value" => "A3"],
                        ["title" => "B1 (1000 x 700mm)", "value" => "B1"],
                        ["title" => "B2 (700 x 500mm)", "value" => "B2"],
                    ]
                ],
                'paper_types' => [
                    "title" => "Paper Type",
                    "options" => [
                        ["title" => "Silk", "value" => "Silk", "image" => "SilkPaper.png"],
                    ]
                ],
                'paper_weights' => [
                    "title" => "Paper Weight",
                    "options" => [
                        "Silk" => [
                            ["title" => "170 gsm", "value" => "170"],
                            ["title" => "250 gsm", "value" => "250"],
                        ],
                    ]
                ],
                'quantity_limits' => [
                    'min' => 25,
                    'max' => 10000
                ],
                'printing_options' => [
                    'allowed_methods' => ['Litho', 'Digital'],
                    'conditions' => [
                        'Digital' => [
                            'excluded_sizes' => ['A1', 'A2', 'B1', 'B2']
                        ]
                    ]
                ],
            ],

            // 5. Gift Wraps
            'Gift Wraps' => [
                'formula' => 'Wraps',
                'tax' => ['enabled' => true, 'rate' => '20', 'type' => 'percentage'], // 20% VAT
                'carbon_offset' => [
                    "title" => "Carbon Offset",
                    "description" => "3% extra charges for carbon offset of your order",
                    "options" => [
                        ["title" => "No", "value" => "No", "price_increase" => 0],
                        ["title" => "Yes", "value" => "Yes", "price_increase" => 3],
                    ]
                ],
                // make it separate option for "Folded to 250 x 175mm or Flat" 
                // increase 10 % price for folded option
                'folding_types' => [ // flyer_types
                    "title" => "Folding Option",
                    "options" => [
                        ["title" => "FLAT", "value" => "FLAT", "price_increase" => 0],
                        ["title" => "Folded to 250 x 175mm", "value" => "Folded", "price_increase" => 10]
                    ]
                ],
                'paper_sizes' => [
                    "title" => "Paper Size",
                    "options" => [
                        ["title" => "500 x 700mm", "value" => "C2"],
                    ]
                ],
                'paper_types' => [
                    "title" => "Paper Type",
                    "options" => [
                        ["title" => "Silk", "value" => "Silk", "image" => "SilkPaper.png"],
                        ["title" => "Uncoated", "value" => "Uncoated", "image" => "UncoatedPaper.png"],
                    ]
                ],
                'paper_weights' => [
                    "title" => "Paper Weight",
                    "options" => [
                        "Silk" => [
                            ["title" => "115 gsm", "value" => "115"],
                        ],
                        "Uncoated" => [
                            ["title" => "100 gsm", "value" => "100"],
                        ],
                    ]
                ],
                'quantity_limits' => [
                    'min' => 250,
                    'max' => 25000
                ],
                'printing_options' => [
                    'allowed_methods' => ['Litho']
                ],
            ],

            // 7. Letterheads
            'Letterheads' => [
                'formula' => 'Letterheads',
                'tax' => ['enabled' => true, 'rate' => '20', 'type' => 'percentage'], // 20% VAT
                'carbon_offset' => [
                    "title" => "Carbon Offset",
                    "description" => "3% extra charges for carbon offset of your order",
                    "options" => [
                        ["title" => "No", "value" => "No", "price_increase" => 0],
                        ["title" => "Yes", "value" => "Yes", "price_increase" => 3],
                    ]
                ],
                'paper_sizes' => [
                    "title" => "Paper Size",
                    "options" => [
                        ["title" => "A4", "value" => "A4"],
                    ]
                ],
                'paper_types' => [
                    "title" => "Paper Type",
                    "options" => [
                        ["title" => "Luxury Uncoated", "value" => "Uncoated", "image" => "UncoatedPaper.png"],
                    ]
                ],
                'paper_weights' => [
                    "title" => "Paper Weight",
                    "options" => [
                        "Uncoated" => [
                            ["title" => "120 gsm", "value" => "120"],
                        ],
                    ]
                ],
                'printed_sides' => [
                    "title" => "Printed Sides",
                    "options" => [
                        ["title" => "Single Side", "value" => "Single Side"],
                        ["title" => "Both Sides", "value" => "Both Sides"]
                    ]
                ],
                'quantity_limits' => [
                    'min' => 500,
                    'max' => 25000
                ],
                'printing_options' => [
                    'allowed_methods' => ['Litho', 'Digital']
                ],
            ],

            // we need a seprate formula for these products
            // 6. Business Cards
            'Business Cards' => [
                'formula' => 'Cards',
                'tax' => ['enabled' => true, 'rate' => '20', 'type' => 'percentage'], // 20% VAT
                'paper_sizes' => [
                    "title" => "Paper Size",
                    "options" => [
                        ["title" => "85 x 55mm", "value" => "C25"], // fix this name
                    ]
                ],
                'paper_types' => [
                    "title" => "Paper Type",
                    "options" => [
                        ["title" => "Silk", "value" => "Silk", "image" => "SilkPaper.png"],
                    ]
                ],
                'paper_weights' => [
                    "title" => "Paper Weight",
                    "options" => [
                        "Silk" => [
                            ["title" => "450 gsm", "value" => "450"],
                        ],
                    ]
                ],
                'quantity_limits' => [
                    'min' => 100,
                    'max' => 10000
                ],
                'printing_options' => [
                    'allowed_methods' => ['Digital']
                ],
            ],

            // 8. Desktop Calendars
            'Desktop Calendars' => [
                'formula' => 'Calendars',
                'tax' => ['enabled' => true, 'rate' => '20', 'type' => 'percentage'], // 20% VAT
                'paper_sizes' => [
                    "title" => "Paper Size",
                    "options" => [
                        ["title" => "A5", "value" => "A5"],
                    ]
                ],
                'printed_sides' => [
                    "title" => "Printed Sides",
                    "options" => [
                        ["title" => "Single Side", "value" => "Single Side"],
                        ["title" => "Both Sides", "value" => "Both Sides"]
                    ]
                ],
                'quantity_limits' => [
                    'min' => 50,
                    'max' => 5000
                ],
                'printing_options' => [
                    'allowed_methods' => ['Digital']
                ],
            ],

            // 4. Roller Banners
            'Roller Banners' => [
                'formula' => 'FixedPrice',  // Changed to use fixed price formula
                'tax' => ['enabled' => true, 'rate' => '20', 'type' => 'percentage'],
                'paper_sizes' => [
                    "title" => "Size",
                    "options" => [
                        ["title" => "Premium 800 x 2000mm", "value" => "C4", "price" => 45.00, "description" => "Standard roller banner size"],
                        ["title" => "1200 x 2000mm", "value" => "C5", "price" => 85.00, "description" => "Large format roller banner"],
                    ]
                ],
                'quantity_limits' => [
                    'min' => 1,
                    'max' => 50
                ],
                'printing_options' => [
                    'allowed_methods' => ['Digital']
                ],
            ],

            // Banners
            'Banners' => [
                'formula' => 'Banners',
                'tax' => ['enabled' => true, 'rate' => '20', 'type' => 'percentage'], // 20% VAT
                'paper_sizes' => [
                    "title" => "Paper Size",
                    "options" => [
                        ["title" => "Small", "value" => "Small"],
                        ["title" => "Medium", "value" => "Medium"],
                        ["title" => "Large", "value" => "Large"],
                        ["title" => "Custom", "value" => "Custom", "requires_input" => true],
                    ]
                ],
                'material' => [
                    "title" => "Material",
                    "options" => [
                        ["title" => "440 gsm PVC", "value" => "440 gsm PVC"],
                    ]
                ],
                'eyelets_required' => [
                    "title" => "Eyelets Required?",
                    "options" => [
                        ["title" => "Yes", "value" => "Yes"],
                        ["title" => "No", "value" => "No"],
                    ]
                ],
                'custom_inputs' => [
                    "length" => [
                        "title" => "Length (m)",
                        "placeholder" => "Enter length in meters",
                        "type" => "number",
                        "min" => 0.1,
                        "step" => 0.1,
                    ],
                    "width" => [
                        "title" => "Width (m)",
                        "placeholder" => "Enter width in meters",
                        "type" => "number",
                        "min" => 0.1,
                        "step" => 0.1,
                    ],
                ],
                'quantity_limits' => [
                    'min' => 1,
                    'max' => 100
                ],
                'printing_options' => [
                    'allowed_methods' => ['Digital']
                ],
            ],

            // New product: Greetings Cards
            'Greetings Cards' => [
                'formula' => 'Flyers', // Using Flyers formula for calculation
                'tax' => ['enabled' => true, 'rate' => '20', 'type' => 'percentage'],
                'flyer_types' => [
                    "title" => "Card Type",
                    "options" => [
                        ["title" => "Folded", "value" => "Folded"]
                    ]
                ],
                'paper_sizes' => [
                    "title" => "Folded Size",
                    "options" => [
                        ["title" => "A5 (210 x 148mm)", "value" => "A5"],
                        ["title" => "A6 (148 x 105mm)", "value" => "A6"]
                    ]
                ],
                'paper_types' => [
                    "title" => "Paper",
                    "options" => [
                        ["title" => "Symbol", "value" => "Symbol", "image" => "SymbolPaper.png", 
                         "description" => "Premium white coated paper with exceptional print quality"],
                        ["title" => "Diva Art Digital", "value" => "DivaArt", "image" => "DivaArtPaper.png",
                         "description" => "Premium quality digital art paper with superior finish"],
                        ["title" => "Mono Stucco Tintoretto", "value" => "MonoStucco", "image" => "MonoStuccoPaper.png",
                         "description" => "Elegant textured paper with a distinctive feel"],
                        ["title" => "Brampton Felt Marked", "value" => "BramptonFelt", "image" => "BramptonFeltPaper.png",
                         "description" => "Classic felt-marked finish for a traditional look"],
                        ["title" => "Infoset Uncoated", "value" => "InfosetUncoated", "image" => "InfosetPaper.png",
                         "description" => "Premium uncoated paper for a natural, sophisticated look"],
                        ["title" => "Economy Silk", "value" => "EconomySilk", "image" => "EconomySilkPaper.png",
                         "description" => "Cost-effective silk paper with good print quality"],
                        ["title" => "Recycled Uncoated", "value" => "RecycledUncoated", "image" => "RecycledUncoatedPaper.png",
                         "description" => "Environmentally friendly uncoated paper option"],
                        ["title" => "Kraft", "value" => "Kraft", "image" => "KraftPaper.png",
                         "description" => "Natural brown kraft paper for rustic appeal"],
                        ["title" => "Old Mill", "value" => "OldMill", "image" => "OldMillPaper.png",
                         "description" => "Premium traditional paper with excellent texture"]
                    ]
                ],
                'paper_weights' => [
                    "title" => "Paper Weight",
                    "options" => [
                        "Symbol" => [
                            ["title" => "300 gsm", "value" => "300"]
                        ],
                        "DivaArt" => [
                            ["title" => "350 gsm", "value" => "350"]
                        ],
                        "MonoStucco" => [
                            ["title" => "300 gsm", "value" => "300"]
                        ],
                        "BramptonFelt" => [
                            ["title" => "300 gsm", "value" => "300"]
                        ],
                        "InfosetUncoated" => [
                            ["title" => "400 gsm", "value" => "400"]
                        ],
                        "EconomySilk" => [
                            ["title" => "300 gsm", "value" => "300"]
                        ],
                        "RecycledUncoated" => [
                            ["title" => "300 gsm", "value" => "300"]
                        ],
                        "Kraft" => [
                            ["title" => "300 gsm", "value" => "300"]
                        ],
                        "OldMill" => [
                            ["title" => "320 gsm", "value" => "320"]
                        ]
                    ]
                ],
                'envelopes_required' => [
                    "title" => "Envelopes",
                    "options" => [
                        ["title" => "No Envelope", 
                         "value" => "No", 
                         "price_increase" => 0,
                         "image" => "NoEnvelope.png",
                         "description" => "Cards only without envelopes"],
                        ["title" => "Plain White", 
                         "value" => "Plain White", 
                         "price_increase" => 0.03,
                         "image" => "PlainWhiteEnvelope.png",
                         "description" => "Classic white envelopes, perfect for any occasion"],
                        ["title" => "Poppy Red", 
                         "value" => "Poppy Red", 
                         "price_increase" => 0.04,
                         "image" => "PoppyRedEnvelope.png",
                         "description" => "Vibrant red envelopes for bold, festive statements"],
                        ["title" => "Laid White", 
                         "value" => "Laid White", 
                         "price_increase" => 0.04,
                         "image" => "LaidWhiteEnvelope.png",
                         "description" => "Textured white envelopes with an elegant finish"],
                        ["title" => "Royal Blue", 
                         "value" => "Royal Blue", 
                         "price_increase" => 0.04,
                         "image" => "RoyalBlueEnvelope.png",
                         "description" => "Deep blue envelopes for sophisticated presentation"],
                        ["title" => "Deep Green", 
                         "value" => "Deep Green", 
                         "price_increase" => 0.04,
                         "image" => "DeepGreenEnvelope.png",
                         "description" => "Rich green envelopes ideal for special occasions"]
                    ]
                ],
                'cover_special_options' => [
                    'foiling_required' => [
                        'title' => 'Foiling Required',
                        'options' => [
                            ['title' => 'No', 'value' => 'No'],
                            ['title' => 'Yes', 'value' => 'Yes'],
                        ],
                        'foil_colours' => [
                            'title' => 'Choose foil colour',
                            'options' => [
                                ['title' => 'Gold', 'value' => 'Gold'],
                                ['title' => 'Silver', 'value' => 'Silver'],
                                ['title' => 'Red', 'value' => 'Red']
                            ]
                        ]
                    ],
                    'embossing_required' => [
                        'title' => 'Embossing Required',
                        'options' => [
                            ['title' => 'No', 'value' => 'No'],
                            ['title' => 'Yes', 'value' => 'Yes'],
                        ]
                    ]
                ],
                'quantity_limits' => [
                    'min' => 50,
                    'max' => 5000
                ],
                'printing_options' => [
                    'allowed_methods' => ['Litho', 'Digital']
                ],
            ],

            // New product: Wedding Invitations
            'Wedding Invitations' => [
                'formula' => 'Flyers', // Using Flyers formula for calculation
                'tax' => ['enabled' => true, 'rate' => '20', 'type' => 'percentage'],
                'flyer_types' => [
                    "title" => "Card Type",
                    "options" => [
                        ["title" => "FLAT", "value" => "FLAT"]
                    ]
                ],
                'paper_sizes' => [
                    "title" => "Flat Size",
                    "options" => [
                        ["title" => "A5 (210 x 148mm)", "value" => "A5"],
                        ["title" => "A6 (148 x 105mm)", "value" => "A6"]
                    ]
                ],
                'paper_types' => [
                    "title" => "Paper",
                    "options" => [
                        ["title" => "Symbol", "value" => "Symbol", "image" => "SymbolPaper.png", 
                         "description" => "Premium white coated paper with exceptional print quality"],
                        ["title" => "Diva Art Digital", "value" => "DivaArt", "image" => "DivaArtPaper.png",
                         "description" => "Premium quality digital art paper with superior finish"],
                        ["title" => "Mono Stucco Tintoretto", "value" => "MonoStucco", "image" => "MonoStuccoPaper.png",
                         "description" => "Elegant textured paper with a distinctive feel"],
                        ["title" => "Brampton Felt Marked", "value" => "BramptonFelt", "image" => "BramptonFeltPaper.png",
                         "description" => "Classic felt-marked finish for a traditional look"],
                        ["title" => "Infoset Uncoated", "value" => "InfosetUncoated", "image" => "InfosetPaper.png",
                         "description" => "Premium uncoated paper for a natural, sophisticated look"],
                        ["title" => "Economy Silk", "value" => "EconomySilk", "image" => "EconomySilkPaper.png",
                         "description" => "Cost-effective silk paper with good print quality"],
                        ["title" => "Recycled Uncoated", "value" => "RecycledUncoated", "image" => "RecycledUncoatedPaper.png",
                         "description" => "Environmentally friendly uncoated paper option"],
                        ["title" => "Kraft", "value" => "Kraft", "image" => "KraftPaper.png",
                         "description" => "Natural brown kraft paper for rustic appeal"],
                        ["title" => "Old Mill", "value" => "OldMill", "image" => "OldMillPaper.png",
                         "description" => "Premium traditional paper with excellent texture"]
                    ]
                ],
                'paper_weights' => [
                    "title" => "Paper Weight",
                    "options" => [
                        "Symbol" => [
                            ["title" => "300 gsm", "value" => "300"]
                        ],
                        "DivaArt" => [
                            ["title" => "350 gsm", "value" => "350"]
                        ],
                        "MonoStucco" => [
                            ["title" => "300 gsm", "value" => "300"]
                        ],
                        "BramptonFelt" => [
                            ["title" => "300 gsm", "value" => "300"]
                        ],
                        "InfosetUncoated" => [
                            ["title" => "400 gsm", "value" => "400"]
                        ],
                        "EconomySilk" => [
                            ["title" => "300 gsm", "value" => "300"]
                        ],
                        "RecycledUncoated" => [
                            ["title" => "300 gsm", "value" => "300"]
                        ],
                        "Kraft" => [
                            ["title" => "300 gsm", "value" => "300"]
                        ],
                        "OldMill" => [
                            ["title" => "320 gsm", "value" => "320"]
                        ]
                    ]
                ],
                'custom_shape' => [
                    "title" => "Custom Shape Required",
                    "options" => [
                        ["title" => "No", "value" => "No"],
                        ["title" => "Yes", "value" => "Yes"],
                    ]
                ],
                'envelopes_required' => [
                    "title" => "Envelopes",
                    "options" => [
                        ["title" => "No Envelope", 
                         "value" => "No", 
                         "price_increase" => 0,
                         "image" => "NoEnvelope.png",
                         "description" => "Invitations only without envelopes"],
                        ["title" => "Plain White", 
                         "value" => "Plain White", 
                         "price_increase" => 0.03,
                         "image" => "PlainWhiteEnvelope.png",
                         "description" => "Classic white envelopes, perfect for weddings"],
                        ["title" => "Poppy Red", 
                         "value" => "Poppy Red", 
                         "price_increase" => 0.04,
                         "image" => "PoppyRedEnvelope.png",
                         "description" => "Vibrant red envelopes for bold, romantic themes"],
                        ["title" => "Laid White", 
                         "value" => "Laid White", 
                         "price_increase" => 0.04,
                         "image" => "LaidWhiteEnvelope.png",
                         "description" => "Premium textured white envelopes for elegant invitations"],
                        ["title" => "Royal Blue", 
                         "value" => "Royal Blue", 
                         "price_increase" => 0.04,
                         "image" => "RoyalBlueEnvelope.png",
                         "description" => "Deep blue envelopes for sophisticated invitations"],
                        ["title" => "Deep Green", 
                         "value" => "Deep Green", 
                         "price_increase" => 0.04,
                         "image" => "DeepGreenEnvelope.png",
                         "description" => "Rich green envelopes for nature-themed weddings"]
                    ]
                ],
                'cover_special_options' => [
                    'foiling_required' => [
                        'title' => 'Foiling Required',
                        'options' => [
                            ['title' => 'No', 'value' => 'No'],
                            ['title' => 'Yes', 'value' => 'Yes'],
                        ],
                        'foil_colours' => [
                            'title' => 'Choose foil colour',
                            'options' => [
                                ['title' => 'Gold', 'value' => 'Gold'],
                                ['title' => 'Silver', 'value' => 'Silver'],
                                ['title' => 'Red', 'value' => 'Red']
                            ]
                        ]
                    ],
                    'embossing_required' => [
                        'title' => 'Embossing Required',
                        'options' => [
                            ['title' => 'No', 'value' => 'No'],
                            ['title' => 'Yes', 'value' => 'Yes'],
                        ]
                    ]
                ],
                'quantity_limits' => [
                    'min' => 25,
                    'max' => 1000
                ],
                'printing_options' => [
                    'allowed_methods' => ['Digital']
                ],
            ],
        ];

        $products = [
            [
                'category_ids' => [1, 2, 3, 4, 5, 6],
                'name' => 'Booklets',
                'description' => 'Designed for magazines, lookbooks, catalogues, or reports, our custom-printed booklets offer exceptional print quality with fully personalised design options. Choose between the durable PUR binding or classic Stitched binding, both providing a polished finish. Select from a wide variety of paper types, sizes, and weights to perfectly showcase your vision. Moreover, enhance your booklets with premium add-ons like cover types, lamination, embossing, and foiling. At SpotPrint, we bring your bold, creative ideas to life with beautifully printed booklets.',
                'price' => 4.50,
                'stock' => 100,
                'is_active' => true,
                'options' => $productOptions['Booklets'],
                'metadata' => ['images' => ['Booklet-01.jpg'], 'features' => ['Stitched binding', 'High-quality paper']],
            ],
            [
                'category_ids' => [1, 2, 3, 4, 5, 6],
                'name' => 'Paperback books',
                'description' => 'Whether you are publishing novels, manuals, autobiographies, or memoirs, experience professional-grade quality with flexible design options with our custom-printed paperback books. Available in durable PUR binding style only. Choose from a diverse range of paper types, stocks, and sizes according to your preference. For a more refined look, add optional premium finishes like cover types, lamination, embossing, foiling, and a carbon offset option. At SpotPrint, we transform your manuscript into a professional book ready to hit the stores. ',
                'price' => 4.50,
                'stock' => 100,
                'is_active' => true,
                'options' => array_merge_recursive_distinct($productOptions['Booklets'], $productOptions['Paperback books']),
                'metadata' => ['images' => ['Paperback-Booklet-01.jpg'], 'features' => ['Stitched binding', 'High-quality paper']],
            ],
            [
                'category_ids' => [1, 2, 3, 4, 5, 6],
                'name' => 'Flyers',
                'description' => 'Spread your message with our striking and eye-catching custom printed flat flyers. Perfect for events, menus, or promotions, our flyers offer various paper sizes, types, and weights to suit your particular brand style. Optional add-ons like single or double-sided printing and the carbon offset option are also available. At SpotPrint, we are determined to provide high-impact flyers with premium print quality without compromising sustainability.',
                'price' => 0.10,
                'stock' => 1000,
                'is_active' => true,
                'options' => $productOptions['Flyers'],
                'metadata' => ['images' => ['Flyer.jpg'], 'features' => ['Single or double-sided print', 'Multiple paper options']],
            ],
            [
                'category_ids' => [1, 2, 3, 4, 5, 6],
                'name' => 'Folded Flyers',
                'description' => 'If you want to grab attention and deliver your message with impact, our customisable folded flyers are perfect. Ideal for menus, event programmes, promotional handouts, or announcements. Choose from a variety of folding options, paper sizes, types, and weights. Furthermore, add flair with your personal style by adding enhancements like single or double-sided printing as well as a carbon offset option. With SpotPrint, turn your creative ideas into sleek folded flyers, crafted with precision to inform and impress.',
                'price' => 0.15,
                'stock' => 1000,
                'is_active' => true,
                'options' => array_merge_recursive_distinct($productOptions['Flyers'], $productOptions['Folded Flyers']),
                'metadata' => ['images' => ['Folded-Flyers.jpg'], 'features' => ['Multiple folding options', 'Professional finish']],
            ],
            [
                'category_ids' => [1, 2, 3, 4, 5, 6],
                'name' => 'Takeaway Menus',
                'description' => "Showcase your dishes beautifully using our custom-printed takeaway menus tailored to your specific style. Whether you need it for elegant restaurants, cosy cafes, bustling food carts, or catering services, SpotPrint’s expertly produced takeaway menus combine crisp text and eye-catching images along with durable paper stock. Additionally, you can choose your preferred paper size, type, and weight from a wide array of options. Enhance your design by choosing between one-sided or double-sided print and carbon offset options.",
                'price' => 0.15,
                'stock' => 500,
                'is_active' => true,
                'options' => array_merge_recursive_distinct($productOptions['Flyers'], $productOptions['Takeaway Menus']),
                'metadata' => ['images' => ['Menus.png'], 'features' => ['Folded design', 'High-quality paper']],
            ],
            [
                'category_ids' => [1, 2, 3, 4, 5, 6],
                'name' => 'Posters',
                'description' => 'Make a bold statement with our custom-printed attention-grabbing posters. Perfect for promoting an event, advertising a product, or decorating a space, our posters deliver crisp and vivid colours and text with a sleek finish. Available in only silk paper type with multiple options for paper size and weight. Additionally, a carbon offset option is also available for eco-conscious customers. At SpotPrint, we transform your vision into striking posters that demand attention.',
                'price' => 2.00,
                'stock' => 500,
                'is_active' => true,
                'options' => $productOptions['Posters'],
                'metadata' => ['images' => ['Poster.jpg'], 'features' => ['Available in multiple paper weights', 'Vibrant color print']],
            ],
            [
                'category_ids' => [1, 2, 3, 4, 5, 6],
                'name' => 'Gift Wraps',
                'description' => 'Wrap your gifts in style with our custom-printed gift wraps designed to make the packaging as special as the gift inside. Suited for personal gifting, boutiques, brands, or seasonal gifting, our gift wraps deliver vivid colours and text as well as premium paper quality.
                Available in Silk and Uncoated paper types, yet provided with a single standard paper size and weight for consistent quality. Furthermore, a carbon offset option is also provided so you can highlight the style or occasion while being responsible. Turn your thoughtful gifts into an unforgettable memory with SpotPrint.',
                'price' => 1.50,
                'stock' => 300,
                'is_active' => true,
                'options' => $productOptions['Gift Wraps'],
                'metadata' => ['images' => ['Giftwraps.png', 'Giftwraps2.png'], 'features' => ['Folded and flat options available', 'Vibrant and colorful designs']],
            ],
            [
                'category_ids' => [1, 2, 3, 4, 5, 6],
                'name' => 'Letterheads',
                'description' => "Perfect for invoices, legal documents, letters, or quotes, our letterheads are printed on premium paper stock that reflects your brand’s professionalism. Available in luxury uncoated paper type with standard paper size and weight. Choose between single or double-sided printing to best represent your business style. At SpotPrint, we help your documents look polished and reliable every time.",
                'price' => 5.00,
                'stock' => 500,
                'is_active' => true,
                'options' => $productOptions['Letterheads'],
                'metadata' => ['images' => ['Letterhead.jpg'], 'features' => ['Available in luxury uncoated paper', 'Single or double-sided print']],
            ],
            [
                'category_ids' => [1, 2, 3, 4, 5, 6],
                'name' => 'Business Cards',
                'description' => "Make a strong and credible first impression with our exceptionally printed custom business cards. Designed to be a powerful extension of your brand, each card is produced in a standard size and weight for maximum impact. Additionally, our premium business cards are printed on high-quality silk paper for a sleek finish. With SpotPrint’s high-end business cards, impress your potential clients and build long-term connections.",
                'price' => 15.00,
                'stock' => 200,
                'is_active' => true,
                'options' => $productOptions['Business Cards'],
                'metadata' => ['images' => ['Business-Cards.png'], 'features' => ['Thick card stock', 'Customizable design']],
            ],
            /* [
                'category_ids' => [1, 2, 3, 4, 5, 6],
                'name' => 'Banners',
                'description' => 'Custom banners available in various sizes and materials.',
                'price' => 20.00,
                'stock' => 100,
                'is_active' => true,
                'options' => $productOptions['Banners'],
                'metadata' => ['images' => ['banner-1.webp', 'banner-2.webp'], 'features' => ['Custom sizes', 'Durable material']],
            ], */
            [
                'category_ids' => [1, 2, 3, 4, 5, 6],
                'name' => 'Greetings Cards',
                'description' => "At SpotPrint, we specialise in transforming your heartfelt messages into beautifully printed greeting cards accompanied by matching envelope options. Perfect for holidays, invitations, or thank-you notes, our cards are available in different finishes with flawless colour and detail. Choose from a wide variety of high-end paper options to best represent your style. Furthermore, add-ons like foiling and embossing are also available. To make the cards even more special, choose between different envelope options that perfectly complement the card’s intention.",
                'price' => 1.50,
                'stock' => 500,
                'is_active' => true,
                'options' => $productOptions['Greetings Cards'],
                'metadata' => ['images' => ['Greeting-Cards.png'], 
                             'features' => ['Premium paper options', 'Foiling and embossing available', 'Matching envelopes']],
            ],
            [
                'category_ids' => [1, 2, 3, 4, 5, 6],
                'name' => 'Wedding Invitations',
                'description' => "Make your special day even more memorable with our premium quality custom-printed wedding invitations. Create your perfect wedding card and choose from a wide variety of premium paper types, available in a flat size of A5 and A6. To add elegance to your wedding invite, enhancements like foiling and embossing are also available. At SpotPrint, we value your individual style; that's why we offer custom shape options as well as an array of envelope styles to best complement your aesthetic. ",
                'price' => 2.00,
                'stock' => 500,
                'is_active' => true,
                'options' => $productOptions['Wedding Invitations'],
                'metadata' => ['images' => ['Wedding-Invitations.png', 'Wedding-Invitations2.png'], 
                             'features' => ['Luxury paper options', 'Custom shapes available', 'Premium finishes']],
            ],
            [
                'category_ids' => [1, 2, 3, 4, 5, 6],
                'name' => 'Roller Banners',
                'description' => "Display your brand with our custom, high-end roller banners that come with sturdy hardware for convenient setup and transportation. Perfect for presentations, trade exhibitions, events, as well as retail displays, our banners are printed on sustainable yet high-quality materials to enhance your business’s presence. Available in two different sizes, SpotPrint’s banners are designed to showcase your message with clarity, impact, and style.",
                'price' => 45.00, // Base price for smallest size
                'stock' => 100,
                'is_active' => true,
                'options' => $productOptions['Roller Banners'],
                'metadata' => [
                    'images' => ['roller-banner-1.jpg', 'roller-banner-2.jpg'], 
                    'features' => [
                        'Complete with hardware',
                        'High-quality printing',
                        'Easy assembly',
                        'Includes carry bag'
                    ]
                ],
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
