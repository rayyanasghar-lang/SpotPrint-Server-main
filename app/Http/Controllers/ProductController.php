<?php
namespace App\Http\Controllers;

use App\Models\Product;
use App\Traits\DataTableTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    use DataTableTrait;

    private $productOptions = [
        'formula'               => 'Booklets',
        'tax'                   => ['enabled' => false, 'rate' => '0', 'type' => 'fixed'],
        'carbon_offset'         => [
            "title"       => "Carbon Offset",
            "description" => "3% extra charges for carbon offset of your order",
            "options"     => [
                ["title" => "No", "value" => "No", "price_increase" => 0],
                ["title" => "Yes", "value" => "Yes", "price_increase" => 3],
            ],
        ],
        'eyelets_required'      => [
            "title"   => "Eyelets Required?",
            "options" => [
                ["title" => "Yes", "value" => "Yes"],
                ["title" => "No", "value" => "No"],
            ],
        ],
        'pages'                 => [
            "title"   => "Pages",
            "options" => [
                'Stitched' => [
                    ["start" => "8", "end" => "48", "step" => "4"],
                ],
                'PUR'      => [
                    ["start" => "16", "end" => "876", "step" => "2"],
                ],
            ],
        ],
        'custom_shape'          => [
            "title"   => "Custom Shape Required",
            "options" => [
                ["title" => "No", "value" => "No"],
                ["title" => "Yes", "value" => "Yes"],
            ],
        ],
        'binding'               => [
            "title"   => "Binding",
            "options" => [
                ["title" => "Stitched", "value" => "Stitched", "image" => "Stitched.png", 'cover' => 'optional', 'required_spin_image' => 'no'],
                ["title" => "PUR", "value" => "PUR", "image" => "PurBinding.png", 'cover' => 'required', 'required_spin_image' => 'yes'],
            ],
        ],
        'orientation'           => [
            "title"   => "Paper Orientation",
            "options" => [
                ["title" => "Portrait", "value" => "Portrait"],
                ["title" => "Landscape", "value" => "Landscape"],
            ],
        ],
        'paper_sizes'           => [
            "title"   => "Paper Size",
            "options" => [
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
                ["title" => "500 x 700mm", "value" => "C2"],
            ],
        ],
        'paper_types'           => [
            "title"   => "Paper Type",
            "options" => [
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
                ["title" => "Luxury Uncoated", "value" => "Luxury Uncoated", "image" => "LuxuryUncoatedPaper.png"],
            ],
        ],
        'paper_weights'         => [
            "title"   => "Paper Weight",
            "options" => [
                "Gloss"             => [
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
                    ["title" => "450 gsm", "value" => "450"],
                ],
                "Silk"              => [
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
                    ["title" => "450 gsm", "value" => "450"],
                ],
                "Uncoated"          => [
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
                    ["title" => "450 gsm", "value" => "450"],
                ],
                "Recycled Silk"     => [
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
                    ["title" => "450 gsm", "value" => "450"],
                ],
                "Recycled Uncoated" => [
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
                    ["title" => "450 gsm", "value" => "450"],
                ],
                "Symbol"            => [
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
                    ["title" => "450 gsm", "value" => "450"],
                ],
                "DivaArt"           => [
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
                    ["title" => "450 gsm", "value" => "450"],
                ],
                "MonoStucco"        => [
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
                    ["title" => "450 gsm", "value" => "450"],
                ],
                "BramptonFelt"      => [
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
                    ["title" => "450 gsm", "value" => "450"],
                ],
                "InfosetUncoated"   => [
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
                    ["title" => "450 gsm", "value" => "450"],
                ],
                "EconomySilk"       => [
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
                    ["title" => "450 gsm", "value" => "450"],
                ],
                "Kraft"             => [
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
                    ["title" => "450 gsm", "value" => "450"],
                ],
                "OldMill"           => [
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
                    ["title" => "450 gsm", "value" => "450"],
                ],
                "Luxury Uncoated"   => [
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
                    ["title" => "450 gsm", "value" => "450"],
                ],
            ],
        ],
        'lamination'            => [
            "title"   => "Lamination",
            "options" => [
                ["title" => "Matt", "value" => "Matt", "image" => "MattLamination.png"],
                ["title" => "Gloss", "value" => "Gloss", "image" => "GlossLamination.png"],
                ["title" => "Soft Touch", "value" => "Soft Touch", "image" => "SoftTouchLamination.png"],
                ["title" => "Anti Scuff Matt", "value" => "Anti Scuff Matt", "image" => "AntiScuffMattLamination.png"],
            ],
        ],
        'cover_special_options' => [
            'foiling_required'   => [
                'title'        => 'Foiling Required',
                'options'      => [
                    ['title' => 'No', 'value' => 'No'],
                    ['title' => 'Yes', 'value' => 'Yes'],
                ],
                'foil_colours' => [
                    'title'   => 'Choose foil colour',
                    'options' => [
                        ['title' => 'Gold', 'value' => 'Gold'],
                        ['title' => 'Silver', 'value' => 'Silver'],
                        ['title' => 'Red', 'value' => 'Red'],
                    ],
                ],
            ],
            'embossing_required' => [
                'title'   => 'Embossing Required',
                'options' => [
                    ['title' => 'No', 'value' => 'No'],
                    ['title' => 'Yes', 'value' => 'Yes'],
                ],
            ],
        ],
        'quantity_limits'       => [
            'min' => 50,
            'max' => 5000,
        ],
        'printing_options'      => [
            'allowed_methods' => ['Litho', 'Digital'],
        ],
        'material'              => [
            "title"   => "Material",
            "options" => [
                ["title" => "440 gsm PVC", "value" => "440 gsm PVC"],
            ],
        ],
        'envelopes_required'    => [
            "title"   => "Envelopes",
            "options" => [
                ["title"         => "No Envelope",
                    "value"          => "No",
                    "price_increase" => 0,
                    "image"          => "NoEnvelope.png",
                    "description"    => "Cards only without envelopes"],
                ["title"         => "Plain White",
                    "value"          => "Plain White",
                    "price_increase" => 0.03,
                    "image"          => "PlainWhiteEnvelope.png",
                    "description"    => "Classic white envelopes, perfect for any occasion"],
                ["title"         => "Poppy Red",
                    "value"          => "Poppy Red",
                    "price_increase" => 0.04,
                    "image"          => "PoppyRedEnvelope.png",
                    "description"    => "Vibrant red envelopes for bold, festive statements"],
                ["title"         => "Laid White",
                    "value"          => "Laid White",
                    "price_increase" => 0.04,
                    "image"          => "LaidWhiteEnvelope.png",
                    "description"    => "Textured white envelopes with an elegant finish"],
                ["title"         => "Royal Blue",
                    "value"          => "Royal Blue",
                    "price_increase" => 0.04,
                    "image"          => "RoyalBlueEnvelope.png",
                    "description"    => "Deep blue envelopes for sophisticated presentation"],
                ["title"         => "Deep Green",
                    "value"          => "Deep Green",
                    "price_increase" => 0.04,
                    "image"          => "DeepGreenEnvelope.png",
                    "description"    => "Rich green envelopes ideal for special occasions"],
            ],
        ],
        'folding_types'         => [
            "title"   => "Folding Option",
            "options" => [
                ["title" => "Roll Folded", "value" => "Roll Folded", "image" => "RollFolded.png"],
                ["title" => "Z Fold (Concertina)", "value" => "Z Fold (Concertina)", "image" => "ZFold.png"],
                ["title" => "Half Folded", "value" => "Half Folded", "image" => "HalfFolded.png"],
                ["title" => "Other (Will be confirmed prior to print)", "value" => "Other", "image" => "OtherFolded.png"],
            ],
        ],
        'printed_sides'         => [
            "title"   => "Printed Sides",
            "options" => [
                ["title" => "Single Side", "value" => "Single Side"],
                ["title" => "Both Sides", "value" => "Both Sides"],
            ],
        ],
        'flyer_types'           => [
            "title"   => "Card Type",
            "options" => [
                ["title" => "FLAT", "value" => "FLAT"],
                ["title" => "Folded", "value" => "Folded"],
            ],
        ]
    ];

    private $validationRules = [
        'category_ids' => 'required',
        'name'         => 'required|string|max:255',
        'description'  => 'nullable|string',
        'price'        => 'required|numeric|min:0',
        'stock'        => 'required|integer|min:0',
        'is_active'    => 'required|boolean',
        'options'      => 'nullable',
        'metadata'     => 'nullable',
    ];

    public function index(Request $request)
    {
        $filters = json_decode(request('filters')) ?? [];
        $query   = Product::query();

        if (isset($filters->category_ids) && is_array($filters->category_ids)) {
            $query->where(function ($q) use ($filters) {
                foreach ($filters->category_ids as $catId) {
                    $q->orWhereJsonContains('category_ids', $catId);
                }
            });
            unset($filters->category_ids);
        }

        $searchColumns = ['name'];
        $products      = $this->dataTable($query, $searchColumns, $filters);

        return $this->successResponse($products, 'List of Products retrieved successfully', 200);
    }

    public function show($id)
    {
        $product = Product::find($id);
        if (! $product) {
            return $this->errorResponse(null, 'Product not found', 404);
        }

        return $this->successResponse($product, 'Product retrieved successfully', 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_ids' => 'required',
            'name'         => 'required|string|max:255',
            'description'  => 'nullable|string',
            'price'        => 'required|numeric|min:0',
            'stock'        => 'required|integer|min:0',
            'is_active'    => 'required|boolean',
            'options'      => 'nullable',
            'metadata'     => 'nullable',
        ]);

        // ----------------------------- Store the Product -----------------------------
        $product = Product::create([
            'category_ids' => $validated['category_ids'],
            'name'         => $validated['name'],
            'description'  => $validated['description'],
            'price'        => $validated['price'],
            'stock'        => $validated['stock'],
            'is_active'    => $validated['is_active'],
            'metadata'     => $validated['metadata'] ?? [],
        ]);

        return $this->successResponse($product, 'Product created successfully', 201);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'category_ids' => 'required',
            'name'         => 'required|string|max:255',
            'description'  => 'nullable|string',
            'price'        => 'required|numeric|min:0',
            'stock'        => 'required|integer|min:0',
            'is_active'    => 'required|boolean',
            'options'      => 'nullable',
            'metadata'     => 'nullable',
        ]);

        $product = Product::find($id);
        if (! $product) {
            return $this->errorResponse('', 'Product Not found', 422);
        }

        // ----------------------------- Update the Product -----------------------------
        $product->update([
            'category_ids' => $validated['category_ids'],
            'name'         => $validated['name'],
            'description'  => $validated['description'],
            'price'        => $validated['price'],
            'stock'        => $validated['stock'],
            'is_active'    => $validated['is_active'],
            // options intentionally not processed here (managed via separate endpoints)
            'metadata'     => $validated['metadata'] ?? [],
        ]);

        return $this->successResponse($product, 'Product updated successfully', 200);
    }

    public function destroy($id)
    {
        $product = Product::find($id);
        if (! $product) {
            return $this->errorResponse(null, 'Product not found', 404);
        }

        $product->delete();
        return $this->successResponse(null, 'Product deleted successfully', 200);
    }    
    



    /**
     * Update product options using the structured form input.
     * Expects request body to contain 'options' (form-like payload).
     * Uses makeProductOptions() to build final options structure.
     */
    public function updateOptions(Request $request, $id)
    {
        $product = Product::find($id);
        if (! $product) {
            return $this->errorResponse(null, 'Product not found', 404);
        }

        // Build final options from the provided form-style input
        $finalOptions = $this->makeProductOptions($request);

        // Save options (can be empty array)
        $product->options = $finalOptions ?? [];
        $product->save();

        return $this->successResponse($product, 'Product options updated successfully', 200);
    }

    /**
     * Update product options by accepting raw JSON payload.
     * Accepts either:
     *  - raw object in the request body (e.g. { ... } ), or
     *  - wrapped as { options: { ... } }
     */
    public function updateJson(Request $request, $id)
    {
        $product = Product::find($id);
        if (! $product) {
            return $this->errorResponse(null, 'Product not found', 404);
        }

        // Accept raw payload or wrapped { options: ... }
        $payload = $request->all();

        // If the request was a direct JSON body representing options, $payload will be that array.
        // If frontend sent { options: { ... } }, prefer that.
        if (isset($payload['options']) && is_array($payload['options'])) {
            $options = $payload['options'];
        } else {
            // Use payload as options (may be empty array)
            $options = is_array($payload) ? $payload : [];
        }

        $product->options = $options;
        $product->save();

        return $this->successResponse($product, 'Product JSON options updated successfully', 200);
    }

    private function makeProductOptions($request)
    {
        $rawOptions = $request->input('options', []);

        $finalOptions = [];

        // Load your full $productOptions reference (titles, options, etc.)
        $productOptions = $this->productOptions;

        $fields = ['pages'];

        foreach ($fields as $field) {
            if (!isset($rawOptions[$field]) || empty($rawOptions[$field])) {
                continue; // skip if not present
            }

            // Ensure it's an array (in case single value is sent)
            $selected = (array) $rawOptions[$field];

            if (isset($productOptions[$field])) {
                $finalOptions[$field] = [
                    'title'   => $productOptions[$field]['title'],
                    'options' => [],
                ];

                foreach ($selected as $sel) {
                    if (isset($productOptions[$field]['options'][$sel])) {
                        $finalOptions[$field]['options'][$sel] = $productOptions[$field]['options'][$sel];
                    }
                }
            }
        }

        // ----------------------------- Rule 1: Yes/No fields -----------------------------
        $yesNoFields = [
            'carbon_offset',
            'eyelets_required',
            'custom_shape',
            'cover_special_options' => 'embossing_required',
        ];

        foreach ($yesNoFields as $parentKey => $field) {
            // Handle top-level keys
            if (is_int($parentKey)) {
                if (($rawOptions[$field] ?? '') === 'Yes' && isset($productOptions[$field])) {
                    $finalOptions[$field] = $productOptions[$field];
                }
            } // Handle nested keys (e.g., cover_special_options.embossing_required)
            else {
                if (($rawOptions[$field] ?? '') === 'Yes' &&
                    isset($productOptions[$parentKey]) &&
                    isset($productOptions[$parentKey][$field])) {

                    $finalOptions[$parentKey][$field] = $productOptions[$parentKey][$field];
                }
            }
        }

        // ----------------------------- Rule 2: Parent Yes + _choice -----------------------------
        $parentWithChoice = [
            'material',
            'envelopes_required',
            'cover_special_options' => 'foiling_required',
        ];

        foreach ($parentWithChoice as $parentKey => $field) {
            // Case 1: flat field (no parent)
            if (is_int($parentKey)) {
                $field = $parentWithChoice[$parentKey];
                if (($rawOptions[$field] ?? '') === 'Yes' && isset($rawOptions["{$field}_choice"])) {
                    $selectedTitles   = $rawOptions["{$field}_choice"] ?? [];
                    $availableOptions = $productOptions[$field]['options'] ?? [];

                    $mergedOptions = collect($availableOptions)->filter(function ($option) use ($selectedTitles) {
                        return in_array($option['title'], $selectedTitles);
                    })->values()->all();

                    $base['title']   = $productOptions[$field]['title'] ?? '';
                    $base['options'] = $mergedOptions;

                    $finalOptions[$field] = $base;
                }
            } // Case 2: nested field with parent
            else {
                if (($rawOptions[$field] ?? '') === 'Yes') {
                    if ($field !== 'foiling_required' && isset($rawOptions["{$field}_choice"])) {
                        $selectedTitles   = $rawOptions["{$field}_choice"] ?? [];
                        $availableOptions = $productOptions[$parentKey][$field]['options'] ?? [];

                        $mergedOptions = collect($availableOptions)->filter(function ($option) use ($selectedTitles) {
                            return in_array($option['title'], $selectedTitles);
                        })->values()->all();

                        $base['title']   = $productOptions[$parentKey][$field]['title'] ?? '';
                        $base['options'] = $mergedOptions;

                        $finalOptions[$parentKey][$field] = $base;

                    } else if ($field === 'foiling_required') {
                        $selectedTitles   = $rawOptions["foil_colours"] ?? [];
                        $availableOptions = $productOptions[$parentKey][$field] ?? [];

                        $allOptions     = $availableOptions['foil_colours']['options'] ?? [];
                        $matchedOptions = collect($allOptions)->filter(function ($option) use ($selectedTitles) {
                            return in_array($option['title'], $selectedTitles);
                        })->values()->all();

                        $availableOptions['foil_colours'] = [
                            'title'   => $productOptions['foil_colours']['title'] ?? '',
                            'options' => $matchedOptions,
                        ];

                        $finalOptions[$parentKey][$field] = $availableOptions;
                    }
                }
            }
        }

        // ----------------------------- Rule 3: paper_types + paper_weights -----------------------------
        if (! empty($rawOptions['paper_types']) && ! empty($rawOptions['paper_weights'])) {
            // Reference your master options
            $optionPaperType   = $productOptions['paper_types'] ?? [];
            $optionPaperWeight = $productOptions['paper_weights'] ?? [];

            // Initialize final array structures
            $finalPaperTypes = [
                "title"   => "Paper Type",
                "options" => [],
            ];

            $finalPaperWeights = [
                "title"   => "Paper Weight",
                "options" => [],
            ];

            // Filter paper_types using $optionPaperType
            if (! empty($optionPaperType['options'])) {
                foreach ($rawOptions['paper_types'] as $rawType) {
                    foreach ($optionPaperType['options'] as $typeOption) {
                        if ($typeOption['value'] === $rawType) {
                            $finalPaperTypes['options'][] = $typeOption;
                            break;
                        }
                    }
                }
            }

            // Filter paper_weights using $optionPaperWeight
            if (! empty($optionPaperWeight['options'])) {
                foreach ($rawOptions['paper_weights'] as $type => $weightsArray) {
                    if (isset($optionPaperWeight['options'][$type])) {
                        $filteredWeights = [];
                        foreach ($weightsArray as $weightItem) {
                            // Validate format
                            if (isset($weightItem['title'], $weightItem['value'])) {
                                $filteredWeights[] = [
                                    'title' => $weightItem['title'],
                                    'value' => $weightItem['value'],
                                ];
                            }
                        }
                        if (! empty($filteredWeights)) {
                            $finalPaperWeights['options'][$type] = $filteredWeights;
                        }
                    }
                }
            }

            // Save to finalOptions using your format
            $finalOptions['paper_types']   = $finalPaperTypes;
            $finalOptions['paper_weights'] = $finalPaperWeights;
        }

        // ----------------------------- Rule 4: Tax Configuration -----------------------------
        if (isset($rawOptions['tax_enabled'], $rawOptions['tax_amount'], $rawOptions['tax_type'])) {
            $finalOptions['tax'] = [
                'enabled' => (bool) $rawOptions['tax_enabled'],
                'rate'    => (float) $rawOptions['tax_amount'],
                'type'    => $rawOptions['tax_type'],
            ];
        }

        // ----------------------------- Rule 5: Quantity Limits -----------------------------
        if (isset($rawOptions['quantity_limits_enabled'])) {
            $finalOptions['quantity_limits'] = [
                'enabled' => (bool) $rawOptions['quantity_limits_enabled'],
                'min'     => $rawOptions['quantity_limits_min'] ?? null,
                'max'     => $rawOptions['quantity_limits_max'] ?? null,
            ];
        }

        // ----------------------------- Rule 6: Remaining Multi-Selects -----------------------------
        $mutiSelectFields = [
            'printing_options',
            'paper_sizes',
            'binding',
            'flyer_types',
            'folding_types',
            'printed_sides',
            'lamination',
            'orientation',
        ];

        foreach ($mutiSelectFields as $key => $fieldMS) {
            // Determine parent and field
            $parent = is_string($key) ? $key : null;
            $field  = $fieldMS;

            if (isset($rawOptions[$field])) {
                // Fetch options
                $optionSet = $parent
                ? ($productOptions[$parent][$field] ?? [])
                : ($productOptions[$field] ?? []);

                // Handle 'printing_options' special case
                if ($field === 'printing_options') {
                    $optionSet['allowed_methods'] = $rawOptions[$field];
                } else {
                    if ($field !== 'orientation') {
                        $optionSet['options'] = collect($optionSet['options'] ?? [])
                            ->filter(function ($option) use ($rawOptions, $field) {
                                return in_array($option['value'], $rawOptions[$field] ?? []);
                            })->values()->all();
                    }
                }

                // Save to finalOptions
                if ($parent) {
                    $finalOptions[$parent][$field] = $optionSet;
                } else {
                    $finalOptions[$field] = $optionSet;
                }
            }
        }

        // formula
        if ($rawOptions['formula']) {
            $finalOptions['formula'] = $rawOptions['formula'] ?? null;
        }

        return $finalOptions;
    }

    

    public function updateStatus(Request $request, $id)
    {
        $product = Product::find($id);
        if (! $product) {
            return $this->errorResponse(null, 'Product not found', 404);
        }

        // Validate the status value (should be 1 for Active or 0 for Inactive)
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:0,1', // Ensure the value is either 0 or 1
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 'Validation failed', 422);
        }

                                                // Update product's is_active status
        $product->is_active = $request->status; // 1 for active, 0 for inactive
        $product->save();

        return response()->json([
            'product' => $product,
            'message' => 'Product status updated successfully',
        ], 200);
    }

    /**
     * Duplicate a product.
     * Creates a new product with same fields but clears images from metadata.
     */
    public function duplicateProduct(Request $request, $id)
    {
        $product = Product::find($id);
        if (! $product) {
            return $this->errorResponse(null, 'Product not found', 404);
        }

        // Prepare metadata: ensure array and clear images
        $metadata = is_array($product->metadata) ? $product->metadata : (is_object($product->metadata) ? (array) $product->metadata : []);
        $metadata['images'] = [];

        // Prepare options (copy as-is)
        $options = $product->options ?? [];

        // Create duplicate - slightly modify name to indicate a copy
        $newName = $product->name . ' (Copy)';

        $newProduct = Product::create([
            'category_ids' => $product->category_ids,
            'name'         => $newName,
            'description'  => $product->description,
            'price'        => $product->price,
            'stock'        => $product->stock,
            'is_active'    => 0,
            'options'      => $options,
            'metadata'     => $metadata,
        ]);

        return $this->successResponse($newProduct, 'Product duplicated successfully', 201);
    }

    public function autocomplete(Request $request)
    {
        $query = $request->input('query');
        if (! $query) {
            return $this->errorResponse(null, 'Query parameter is required', 400);
        }

        $products = Product::where('name', 'LIKE', '%' . $query . '%')
            ->get(['id', 'name']);

        return $this->successResponse($products, '', 200);
    }



    /**** Frontend APIs ****/
    public function getProduct($name)
    {
        $product = Product::where('is_active', true)->where('name', $name)->orderBy('id', 'asc')->first();
            
        if (! $product) {
            return $this->errorResponse(null, 'Product not found', 404);
        }

        return $this->successResponse($product, 'List of Products retrieved successfully', 200);
    }

    public function getProducts($category_id = null)
    {
        if ($category_id) {
            $products = Product::where('is_active', true)->whereJsonContains('category_ids', (int) $category_id)->get();
        } else {
            $products = Product::where('is_active', true)->get();
        }

        return $this->successResponse($products, 'List of Products retrieved successfully', 200);
    }
}
