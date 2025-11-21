<?php
namespace Database\Seeders;

use App\Models\System;
use Illuminate\Database\Seeder;

class SystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $owner = []; // Add owner details if necessary

        $configurations = [
            // Number of pages that can fit on one sheet for different paper sizes
            'pages_in_one_sheet' => [
                'C2' => '2',    // Existing
                'C25' => '25',  // Existing
                'A1' => '2',    // Added for A1 posters
                'A2' => '4',    // Added for A2 posters
                'A3' => '4',    // Existing
                'A4' => '8',    // Existing
                'A5' => '16',   // Existing
                'A6' => '32',   // Existing
                'B1' => '1',    // Added for B1 posters (1000 x 700mm)
                'B2' => '2',    // Added for B2 posters (700 x 500mm)
                'B5' => '8',    // Existing
                'DL' => '24',   // Existing
                '210' => '8',   // Existing
            ],
            'price_per_ton' => [
                'Gloss' => '750',
                'Silk' => '750',
                'Uncoated' => '1200',
                'Recycled Silk' => '1450',
                'Recycled Uncoated' => '1400',
                
                'Specialty'=> '2000',  // Added entry
                'Symbol' => '2200',    // New specialty papers
                'DivaArt' => '2400',
                'MonoStucco' => '2300',
                'BramptonFelt' => '2250',
                'InfosetUncoated' => '2350'
            ],

            'cost_of_plate' => ['litho' => '100', 'digital' => '5'],
            'waste_of_sheets' => ['litho' => '300', 'digital' => '30'],
            'digital_print_cost' => '0.06',

            'shipping_box_size_in_weight' => ['dpd' => '20', 'pallet' => '750'],
            'shipping_price_per_box' => ['dpd' => '7.5', 'pallet' => '65'],

            'foiling_cost' => ['fixed_cost' => 100, 'variable_cost_per_100_units' => 4],
            'embossing_cost' => ['fixed_cost' => 100, 'variable_cost_per_100_units' => 4],
            'custom_shape_cost' => ['fixed_cost' => 100, 'variable_cost_per_100_units' => 4],
            'profit_margin' => ['low' => '0.15', 'high' => '0.10', 'thresh_hold' => '1750'],

            'fixed_price_products' => [
                'roller_banners' => [
                    'C4' => 45.00,  // Premium 800 x 2000mm
                    'C5' => 85.00,  // 1200 x 2000mm
                ]
            ],
        ];

        $global_product_options = [
            "formula" => [
                ["title" => "Booklets", "value" => "Booklets"],
                ["title" => "Flyers", "value" => "Flyers"],
                ["title" => "Posters", "value" => "Posters"],
                ["title" => "Wraps", "value" => "Wraps"],
                ["title" => "Letterheads", "value" => "Letterheads"],
                ["title" => "Cards", "value" => "Cards"],
                ["title" => "Calendars", "value" => "Calendars"],
                ["title" => "Banners", "value" => "Banners"],
                ["title" => "FixedPrice", "value" => "FixedPrice"]
            ],
            "tax" => [
                ["title" => "20% VAT", "value" => ["enabled" => true, "rate" => 20, "type" => "percentage"]],
                ["title" => "No Tax", "value" => ["enabled" => false, "rate" => 0, "type" => "percentage"]]
            ],
            "printing_options" => [
                "allowed_methods" => [
                    ["title" => "Litho", "value" => "Litho"],
                    ["title" => "Digital", "value" => "Digital"]
                ]
            ],
            "quantity_limits" => [
                ["title" => "min", "value" => "25"],
                ["title" => "max", "value" => "50000"]
            ],
            "orientation" => [
                ["title" => "Portrait", "value" => "Portrait"],
                ["title" => "Landscape", "value" => "Landscape"],
            ],
            "paper_sizes" => [
                ["title" => "A1 (594 x 840mm)", "value" => "A1"],
                ["title" => "A2 (594 x 420mm)", "value" => "A2"],
                ["title" => "A3 (420 x 297mm)", "value" => "A3"],
                ["title" => "A4 (297 x 210mm)", "value" => "A4"],
                ["title" => "A5 (210 x 148mm)", "value" => "A5"],
                ["title" => "A6 (148 x 105mm)", "value" => "A6"],
                ["title" => "B1 (1000 x 700mm)", "value" => "B1"],
                ["title" => "B2 (700 x 500mm)", "value" => "B2"],
                ["title" => "B5 (240 x 170mm)", "value" => "B5"],
                ["title" => "DL (210 x 99mm)", "value" => "DL"],
                ["title" => "210 x 210mm", "value" => "210"],
                ["title" => "85 x 55mm", "value" => "C25"],
                ["title" => "Premium 800 x 2000mm", "value" => "C4"],
                ["title" => "1200 x 2000mm", "value" => "C5"],
                ["title" => "500 x 700mm", "value" => "C2"]
            ],
            "paper_types" => [
                ["title" => "Gloss", "value" => "Gloss", "image" => "GlossPaper.png"],
                ["title" => "Silk", "value" => "Silk", "image" => "SilkPaper.png"],
                ["title" => "Uncoated", "value" => "Uncoated", "image" => "UncoatedPaper.png"],
                ["title" => "Recycled Silk", "value" => "Recycled Silk", "image" => "RecycledSilkPaper.png"],
                ["title" => "Recycled Uncoated", "value" => "Recycled Uncoated", "image" => "RecycledUncoatedPaper.png"],
                ["title" => "Symbol", "value" => "Symbol", "image" => "SymbolPaper.png", "description" => "Premium white coated paper with exceptional print quality"],
                ["title" => "Diva Art Digital", "value" => "DivaArt", "image" => "DivaArtPaper.png", "description" => "Premium quality digital art paper with superior finish"],
                ["title" => "Mono Stucco Tintoretto", "value" => "MonoStucco", "image" => "MonoStuccoPaper.png", "description" => "Elegant textured paper with a distinctive feel"],
                ["title" => "Brampton Felt Marked", "value" => "BramptonFelt", "image" => "BramptonFeltPaper.png", "description" => "Classic felt-marked finish for a traditional look"],
                ["title" => "Infoset Uncoated", "value" => "InfosetUncoated", "image" => "InfosetPaper.png", "description" => "Premium uncoated paper for a natural look"],
                ["title" => "Economy Silk", "value" => "EconomySilk", "image" => "EconomySilkPaper.png", "description" => "Cost-effective silk paper with good print quality"],
                ["title" => "Kraft", "value" => "Kraft", "image" => "KraftPaper.png", "description" => "Natural brown kraft paper for rustic appeal"],
                ["title" => "Old Mill", "value" => "OldMill", "image" => "OldMillPaper.png", "description" => "Premium traditional paper with excellent texture"],
                ["title" => "Luxury Uncoated", "value" => "Luxury Uncoated", "image" => "LuxuryUncoatedPaper.png"]
            ],
            "paper_weights" => [
                ["title" => "100 gsm", "value" => "100"],
                ["title" => "110 gsm", "value" => "110"],
                ["title" => "115 gsm", "value" => "115"],
                ["title" => "120 gsm", "value" => "120"],
                ["title" => "130 gsm", "value" => "130"],
                ["title" => "150 gsm", "value" => "150"],
                ["title" => "170 gsm", "value" => "170"],
                ["title" => "190 gsm", "value" => "190"],
                ["title" => "200 gsm", "value" => "200"],
                ["title" => "250 gsm", "value" => "250"],
                ["title" => "300 gsm", "value" => "300"],
                ["title" => "320 gsm", "value" => "320"],
                ["title" => "350 gsm", "value" => "350"],
                ["title" => "400 gsm", "value" => "400"],
                ["title" => "450 gsm", "value" => "450"]
            ],
            "binding" => [
                ["title" => "Stitched", "value" => "Stitched", "image" => "Stitched.png", "cover" => "optional", "required_spin_image" => "no"],
                ["title" => "PUR", "value" => "PUR", "image" => "PurBinding.png", "cover" => "required", "required_spin_image" => "yes"]
            ],
            "flyer_types" => [
                ["title" => "FLAT", "value" => "FLAT"],
                ["title" => "Folded", "value" => "Folded"]
            ],
            "folding_types" => [
                ["title" => "Roll Folded", "value" => "Roll Folded", "image" => "RollFolded.png"],
                ["title" => "Z Fold (Concertina)", "value" => "Z Fold (Concertina)", "image" => "ZFold.png"],
                ["title" => "Half Folded", "value" => "Half Folded", "image" => "HalfFolded.png"],
                ["title" => "Other (Will be confirmed prior to print)", "value" => "Other", "image" => "OtherFolded.png"]
            ],
            "printed_sides" => [
                ["title" => "Single Side", "value" => "Single Side"],
                ["title" => "Both Sides", "value" => "Both Sides"]
            ],
            "lamination" => [
                ["title" => "Matt", "value" => "Matt", "image" => "MattLamination.png"],
                ["title" => "Gloss", "value" => "Gloss", "image" => "GlossLamination.png"],
                ["title" => "Soft Touch", "value" => "Soft Touch", "image" => "SoftTouchLamination.png"],
                ["title" => "Anti Scuff Matt", "value" => "Anti Scuff Matt", "image" => "AntiScuffMattLamination.png"]
            ],
            "cover_special_options" => [
                "foiling_required" => [
                    "title" => "Foiling Required",
                    "options" => [
                        ["title" => "No", "value" => "No"],
                        ["title" => "Yes", "value" => "Yes"]
                    ],
                    "foil_colours" => [
                        "title" => "Choose foil colour",
                        "options" => [
                            ["title" => "Gold", "value" => "Gold"],
                            ["title" => "Silver", "value" => "Silver"],
                            ["title" => "Red", "value" => "Red"]
                        ]
                    ]
                ],
                "embossing_required" => [
                    "title" => "Embossing Required",
                    "options" => [
                        ["title" => "No", "value" => "No"],
                        ["title" => "Yes", "value" => "Yes"]
                    ]
                ]
            ],
            "custom_shape" => [
                "title" => "Custom Shape Required",
                "options" => [
                    ["title" => "No", "value" => "No"],
                    ["title" => "Yes", "value" => "Yes"]
                ]
            ],
            "envelopes_required" => [
                "title" => "Envelopes",
                "options" => [
                    ["title" => "No Envelope", "value" => "No", "price_increase" => 0, "image" => "NoEnvelope.png", "description" => "Without envelopes"],
                    ["title" => "Plain White", "value" => "Plain White", "price_increase" => 0.03, "image" => "PlainWhiteEnvelope.png", "description" => "Classic white envelopes"],
                    ["title" => "Poppy Red", "value" => "Poppy Red", "price_increase" => 0.04, "image" => "PoppyRedEnvelope.png", "description" => "Vibrant red envelopes"],
                    ["title" => "Laid White", "value" => "Laid White", "price_increase" => 0.04, "image" => "LaidWhiteEnvelope.png", "description" => "Textured white envelopes"],
                    ["title" => "Royal Blue", "value" => "Royal Blue", "price_increase" => 0.04, "image" => "RoyalBlueEnvelope.png", "description" => "Deep blue envelopes"],
                    ["title" => "Deep Green", "value" => "Deep Green", "price_increase" => 0.04, "image" => "DeepGreenEnvelope.png", "description" => "Rich green envelopes"]
                ]
            ],
            "material" => [
                "title" => "Material",
                "options" => [
                    ["title" => "440 gsm PVC", "value" => "440 gsm PVC"]
                ]
            ],
            "eyelets_required" => [
                "title" => "Eyelets Required?",
                "options" => [
                    ["title" => "No", "value" => "No"],
                    ["title" => "Yes", "value" => "Yes"]
                ]
            ],
            "carbon_offset" => [
                "title" => "Carbon Offset",
                "description" => "3% extra charges for carbon offset of your order",
                "options" => [
                    ["title" => "No", "value" => "No", "price_increase" => 0],
                    ["title" => "Yes", "value" => "Yes", "price_increase" => 3]
                ]
            ],
            "pages" => [
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
        ];

        $settings = [
            "admin_email" => "admin@spotprint.com",
            "contact_email" => "contact@spotprint.com",
            "phone_number" => "+1234567890",
            "address" => "123 Main Street, London, UK",
            "timezone" => "UTC",
            "currency" => "GBP",
            "meta_title" => "Welcome to SpotPrint",
            "meta_description" => "SpotPrint",
            "meta_keywords" => "print",
            "social_media_links" => [
                "facebook" => "https://www.facebook.com/spotprint",
                "twitter" => "https://www.twitter.com/spotprint",
                "linkedin" => "https://www.linkedin.com/spotprint",
                "instagram" => "https://www.instagram.com/spotprint",
            ],
        ];

        System::create([
            'system_name' => 'SpotPrint',
            'system_url' => 'spotprint.com',
            'owner' => $owner,
            'configurations' => $configurations,
            'global_product_options' => $global_product_options,
            'settings' => $settings,
        ]);
    }
}
