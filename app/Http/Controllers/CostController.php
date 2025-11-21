<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\System;


class CostController extends Controller
{
    /*
    {
        "formula": "Booklets",
        "tax": {
            "rate": "20",
            "type": "percentage",
            "enabled": true
        },
        "quantity": 1,
        "size": "A5",
        "number_of_pages": 4,
        "sheet_type": "Gloss",
        "sheet_weight_in_grams": "130",


        "orientation": "Landscape", // not used
        "flyerType": "",
        "printed_sides": "",
        
        "binding": "Stitched",
        "cover_types": "Gloss",
        "cover_weight_in_grams": "250",
        "selectedDeliverOption": "Standard",
        "lamination": "Matt"
    }   
    */
    public function estimate_cost(Request $request)
    {
        //checking nullable fields
        $validator = Validator::make($request->all(), [
            'formula' => 'nullable',
            'tax' => 'nullable',
            'quantity' => 'nullable',
            'size' => 'nullable',
            'number_of_pages' => 'nullable',
            'sheet_type' => 'nullable',
            'sheet_weight_in_grams' => 'nullable',
            'binding' => 'nullable',
            'foiling_required' => 'nullable',
            'embossing_required' => 'nullable',
            'orientation' => 'nullable',
            'flyer_type' => 'nullable',
            'foldingTypes' => 'nullable',
            'printed_sides' => 'nullable',
            'cover_types' => 'nullable',
            'cover_weight_in_grams' => 'nullable',
            'lamination' => 'nullable',
            'carbon_offset' => 'nullable',
            'selectedDeliverOption' => 'nullable',
            'foling_color' => 'nullable',
            'envelopes_required' => 'nullable',
            'custom_shape_required' => 'nullable',
        ]);
        if ($validator->fails()) return $this->errorResponse($validator->errors(), 'Validation failed', 422);
        $validated = $validator->validated();

        // get system configurations
        //getting configurations field from the system table (the configuration consists of pricing, fixed costs, variable costs, waste percentages, etc.)
        $system = System::select('configurations')->find(1);
        $config = $system['configurations'];


        //getting formula from the validated data
        //only two formulas are supported currently: FixedPrice and Booklets/Flyers/Posters(main calculator() function)
        $formula = $validated['formula'] ?? '';
        switch ($formula) {
            case 'FixedPrice':
                $res = $this->fixed_price_calculator($validated, $config);
                break;

            case 'asdsa':
                break;

            case 'Booklets':
            case 'Flyers':
            case 'Posters': 
            default: // Booklets, Flyers, Posters

               //calculates prices for both litho and digital printing and return the cheaper one
                $litho = $this->main_calculator($validated, $config, 'Litho');
                $digital = $this->main_calculator($validated, $config, 'Digital');
                $res = [];
                if ($digital === null) $res = $litho['main'];
                else {
                    if ($digital['main']['total_price'] <= $litho['main']['total_price']) $res = $digital['main'];
                    else $res = $litho['main'];
                }
                $res['all_calculations'] = ['litho' => $litho, 'digital' => $digital];
                break;
        }
        

        return $this->successResponse($res, '', 200);
    }

    // (printing method is Digital by default)
    private function main_calculator($validated, $config, $printing_method = 'Digital')
    {
        // create variables and get values from validated data, if not available then use default values
        $quantity = $validated['quantity'] ?? 1;
        $size = $validated['size'] ?? '';
        $number_of_pages = $validated['number_of_pages'] ?? 1;
        $sheet_type = $validated['sheet_type'] ?? '';
        $sheet_weight_in_grams = $validated['sheet_weight_in_grams'] ?? 0;
        $binding = $validated['binding'] ?? '';
        $cover_types = $validated['cover_types'] ?? '';

        $foiling_required = $validated['foiling_required'] ?? 'No';
        $embossing_required = $validated['embossing_required'] ?? 'No';
        $custom_shape_required = $validated['custom_shape_required'] ?? 'No';
        $orientation = $validated['orientation'] ?? '';
        $flyer_type = $validated['flyer_type'] ?? '';
        $foldingTypes = $validated['foldingTypes'] ?? '';
        $printed_sides = $validated['printed_sides'] ?? '';
        $cover_weight_in_grams = $validated['cover_weight_in_grams'] ?? '';
        $selectedDeliverOption = $validated['selectedDeliverOption'] ?? '';
        $lamination = $validated['lamination'] ?? '';
        $carbon_offset = $validated['carbon_offset'] ?? 'No';
        $foling_color = $validated['foling_color'] ?? '';
        $tax = $validated['tax'] ?? ['rate' => 0, 'type' => 'percentage', 'enabled' => false];
        $formula = $validated['formula'] ?? '';

        // Add checks for Posters formula
        if ($formula === 'Posters') 
        {
            // Check if digital printing is allowed for this size
            if ($printing_method === 'Digital' && in_array($size, ['A1', 'A2', 'B1', 'B2'])) {
                return null;
            }

            // Handle size-specific calculations
            $multiplier = 1;
            
            switch ($size) {
                case 'A2':
                case 'B1':
                    $multiplier = 2; // 2up costing
                    break;
                case 'B2':
                    $multiplier = 1.2; // Additional 20% for B2
                    break;
            }
        }

        $single_sheet_weight = (0.64 * 0.9 * $sheet_weight_in_grams);
        if($printing_method == 'Digital') $single_sheet_weight = $single_sheet_weight / 4;
        $single_sheet_cost = $single_sheet_weight * ($config['price_per_ton'][$sheet_type] / 1000000);

        $pages_in_one_sheet = $config['pages_in_one_sheet'][$size];
        if($printing_method == 'Digital') $pages_in_one_sheet = $pages_in_one_sheet / 4;

        if($printed_sides == 'Both Sides' || $printed_sides == 'Single Side') $number_of_pages = 2;
        

        $paper_required_for_one_quantity = $number_of_pages / $pages_in_one_sheet;
        $tota_sheets_required = (($quantity * $paper_required_for_one_quantity) / 2);
        $plate_cost = $paper_required_for_one_quantity * 100;

        if ($printing_method == 'Litho') {
            $tota_sheets_required += $config['waste_of_sheets']['litho'];
            $plate_cost_final = $plate_cost > $config['cost_of_plate']['litho'] ? $plate_cost : $config['cost_of_plate']['litho'];
        } else {
            $tota_sheets_required += $config['waste_of_sheets']['digital'];
            $plate_cost_final = $config['cost_of_plate']['digital'];
        }

        $cost_of_papers = $single_sheet_cost * $tota_sheets_required;
        $weight_of_papers_in_kg = ($single_sheet_weight * $tota_sheets_required) / 1000;

        // Add cover calculations
        $cover_cost = 0;
        $cover_weight_in_kg = 0;
        if ($cover_types && $cover_weight_in_grams) {
            // Calculate cover paper weight and cost
            $cover_sheet_weight = (0.64 * 0.9 * $cover_weight_in_grams);
            if ($printing_method == 'Digital') $cover_sheet_weight = $cover_sheet_weight / 4;            
            $cover_sheet_cost = $cover_sheet_weight * ($config['price_per_ton'][$cover_types] / 1000000);
            
            // Calculate sheets needed for cover (4 pages)
            $cover_pages = 4;
            $cover_sheets_required = ($quantity * ($cover_pages / $pages_in_one_sheet)) / 2;
            
            // Add waste sheets for cover
            if ($printing_method == 'Litho') {
                $cover_sheets_required += $config['waste_of_sheets']['litho'];
            } else {
                $cover_sheets_required += $config['waste_of_sheets']['digital'];
            }

            // Calculate total cover cost and weight
            $cover_cost = $cover_sheet_cost * $cover_sheets_required;
            $cover_weight_in_kg = ($cover_sheet_weight * $cover_sheets_required) / 1000;
            
            // Add cover costs to total paper costs
            $cost_of_papers += $cover_cost;
            $weight_of_papers_in_kg += $cover_weight_in_kg;
        }

        if($printing_method == 'Litho')
        {
            $printing_cost = ($tota_sheets_required * 20) / 1000;
            if ($sheet_weight_in_grams > 249) $printing_cost *= 1.2;
            $final_printing_cost = $printing_cost < 60 ? 60 : $printing_cost;
            if($printed_sides == 'Single Side') $final_printing_cost = $final_printing_cost / 2;

            $cutting_cost = ($weight_of_papers_in_kg / 400) * 60;
            $final_cutting_cost = $cutting_cost < 20 ? 20 : $cutting_cost;
        }
        else{
            $final_printing_cost = ( $tota_sheets_required * 2 ) * $config['digital_print_cost'];
            if($printed_sides == 'Single Side') $final_printing_cost = $final_printing_cost / 2;
            $cutting_cost = ($weight_of_papers_in_kg / 400) * 60;
            $final_cutting_cost = $cutting_cost < 5 ? 5 : $cutting_cost;
        }

        

        $folding_cost = 0;
        if ($foldingTypes) {
            $folding_cost = ($quantity/10000 * 25 + 10);
        }

        // stitching cost for booklets only
        $stitching_cost = 0;
        if(!empty($binding))
        {
            $stitching_cost = (($quantity / 3500) * 25) + 15;
            if($binding == 'PUR') $stitching_cost *= 1.2;
            else {
                if ($number_of_pages > 24) {
                    $stitching_cost *= 1.4;
                }
            }
        }

        $cost_of_delivery['dpd'] = (($weight_of_papers_in_kg) / $config['shipping_box_size_in_weight']['dpd']) * $config['shipping_price_per_box']['dpd'];
        $cost_of_delivery['dpd'] = ($cost_of_delivery['dpd'] < $config['shipping_price_per_box']['dpd']) ? $config['shipping_price_per_box']['dpd'] : $cost_of_delivery['dpd'];
        $cost_of_delivery['pallet'] = (($weight_of_papers_in_kg) / $config['shipping_box_size_in_weight']['pallet']) * $config['shipping_price_per_box']['pallet'];
        $cost_of_delivery['pallet'] = ($cost_of_delivery['pallet'] < $config['shipping_price_per_box']['pallet']) ? $config['shipping_price_per_box']['pallet'] : $cost_of_delivery['pallet'];
        $cost_of_delivery['method'] = $cost_of_delivery['dpd'] > $cost_of_delivery['pallet'] ? 'pallet' : 'dpd';
        $cost_of_delivery['final'] = $cost_of_delivery['dpd'] > $cost_of_delivery['pallet'] ? $cost_of_delivery['pallet'] : $cost_of_delivery['dpd'];

        $foiling_cost = 0;
        if ($foiling_required === 'Yes') {
            $foiling_cost = $config['foiling_cost']['fixed_cost'] + ($quantity / 100) * $config['foiling_cost']['variable_cost_per_100_units'];
        }

        $embossing_cost = 0;
        if ($embossing_required === 'Yes') {
            $embossing_cost = $config['embossing_cost']['fixed_cost'] + ($quantity / 100) * $config['embossing_cost']['variable_cost_per_100_units'];
        }

        $custom_shape_cost = 0;
        if ($custom_shape_required === 'Yes') {
            $custom_shape_cost = $config['custom_shape_cost']['fixed_cost'] + ($quantity / 100) * $config['custom_shape_cost']['variable_cost_per_100_units'];
        }

        $lamination_cost = 0;
        if ($lamination) { // validated
            $base_lamination_cost = 7; // Base setup cost
            $per_sqm_cost = [
                'Matt' => 0.40,
                'Gloss' => 0.40,
                'Soft Touch' => 0.60,
                'Anti Scuff Matt' => 0.80
            ];
            
            // Calculate area in square meters 
            $width = 0;
            $height = 0;
            switch($size) {
                case 'A4': $width = 0.297; $height = 0.210; break;
                case 'A5': $width = 0.210; $height = 0.148; break;
                case 'A3': $width = 0.420; $height = 0.297; break;
                case 'DL': $width = 0.210; $height = 0.099; break;
                case 'A6': $width = 0.148; $height = 0.105; break;
                default: $width = 0.210; $height = 0.148; // default to A5
            }
            
            $area_per_piece = $width * $height;
            $total_area = $area_per_piece * $quantity * 2; // double for both sides
            
            // Calculate lamination cost
            $lamination_cost = $base_lamination_cost + ($total_area * ($per_sqm_cost[$lamination] ?? 0.40));
        }

        $carbon_offset_cost = 0;
        if ($carbon_offset === 'Yes') {
            // Calculate 3% of total production cost excluding delivery and other special options
            $production_cost = $plate_cost_final + $cost_of_papers + $final_printing_cost + $final_cutting_cost + $folding_cost + $stitching_cost;
            $carbon_offset_cost = $production_cost * 0.03;
        }

        // Add envelope cost calculation
        $envelope_cost = 0;
        if (!empty($validated['envelopes_required']) && $validated['envelopes_required'] !== 'No') {
            $envelope_prices = [
                'Plain White' => 0.03,
                'Poppy Red' => 0.04,
                'Laid White' => 0.04,
                'Royal Blue' => 0.04,
                'Deep Green' => 0.04
            ];
            $envelope_cost = $quantity * ($envelope_prices[$validated['envelopes_required']] ?? 0);
        }

        $total_cost = $plate_cost_final + $cost_of_papers + $final_printing_cost + $final_cutting_cost; 
        $total_cost += $folding_cost + $stitching_cost + $cost_of_delivery['final'] + $envelope_cost;
        $total_cost += $foiling_cost + $embossing_cost + $custom_shape_cost + $lamination_cost + $carbon_offset_cost;

        // Apply multiplier for Posters if applicable
        if ($formula === 'Posters') {
            $total_cost *= $multiplier;
        }

        $profit = 0;
        if ($total_cost < $config['profit_margin']['thresh_hold']) {
            $profit = $total_cost * $config['profit_margin']['low'];
        } else {
            $profit = $total_cost * $config['profit_margin']['high'];
        }
        $total_price = $total_cost + $profit;


        // Calculate tax if enabled
        $tax_amount = 0;
        if ($tax['enabled']) {
            if ($tax['type'] === 'percentage') {
                $tax_amount = ($total_price * $tax['rate']) / 100;
            } elseif ($tax['type'] === 'fixValue') {
                $tax_amount = $tax['rate'];
            }
            $total_price += $tax_amount;
        }

        // Add delivery options to the response
        $delivery_options = [
            ['delivery_type' => 'Standard', 'working_days' => 5, 'final_price' => $total_price],
            ['delivery_type' => 'Express', 'working_days' => 2, 'final_price' => ($total_price * 1.10)],
        ];

        $res = [
            'printing_cost_obj' => [
                'plate_cost_final' => $plate_cost_final,
                'tota_sheets_required' => $tota_sheets_required,
                'weight_of_papers_in_kg' => $weight_of_papers_in_kg,
                'cost_of_papers' => $cost_of_papers,
                'final_printing_cost' => $final_printing_cost,
                'final_cutting_cost' => $final_cutting_cost,
                'folding_cost' => $folding_cost,
                'stitching_cost' => $stitching_cost,
                'foiling_cost' => $foiling_cost,
                'embossing_cost' => $embossing_cost,
                'custom_shape_cost' => $custom_shape_cost,
                'lamination_cost' => $lamination_cost,
                'carbon_offset_cost' => $carbon_offset_cost,
                'cover_cost' => $cover_cost,
                'cover_weight_in_kg' => $cover_weight_in_kg,
                'total_paper_cost' => $cost_of_papers,
                'total_weight_in_kg' => $weight_of_papers_in_kg,
                'envelope_cost' => $envelope_cost,
            ],
            'total_cost_obj' => [
                'total_cost' => $total_cost,
                'profit' => $profit,
                'tax_amount' => $tax_amount,
                'total_price' => $total_price,
            ],
            'main' => [
                'printing_method' => $printing_method,
                'total_cost' => $total_cost + $profit,
                'tax_amount' => $tax_amount,
                'total_price' => $total_price,
                'delivery_cost_obj' => $cost_of_delivery,
                'delivery_options' => $delivery_options
            ],
        ];

        return $res;
    }

    private function fixed_price_calculator($validated, $config)
    {
        $quantity = $validated['quantity'] ?? 1;
        $size = $validated['size'] ?? '';
        $carbon_offset = $validated['carbon_offset'] ?? 'No';
        $tax = $validated['tax'] ?? ['enabled' => true, 'rate' => '20', 'type' => 'percentage'];
        
        // Get unit price based on size
        $unit_price = $config['fixed_price_products']['roller_banners'][$size] ?? 0;
        $total_cost = $unit_price * $quantity;

        // Add carbon offset if selected
        $carbon_offset_cost = 0;
        if ($carbon_offset === 'Yes') {
            $carbon_offset_cost = $total_cost * 0.03;
        }

        $total_cost += $carbon_offset_cost;

        // Calculate profit margin
        $profit = 0;
        if ($total_cost < $config['profit_margin']['thresh_hold']) {
            $profit = $total_cost * $config['profit_margin']['low'];
        } else {
            $profit = $total_cost * $config['profit_margin']['high'];
        }
        
        $total_price = $total_cost + $profit;

        // Calculate tax if enabled
        $tax_amount = 0;
        if ($tax['enabled']) {
            if ($tax['type'] === 'percentage') {
                $tax_amount = ($total_price * $tax['rate']) / 100;
            }
            $total_price += $tax_amount;
        }

        // Delivery options
        $delivery_options = [
            ['delivery_type' => 'Standard', 'working_days' => 5, 'final_price' => $total_price],
            ['delivery_type' => 'Express', 'working_days' => 2, 'final_price' => ($total_price * 1.10)],
        ];

        return [
            'total_cost' => $total_cost,
            'tax_amount' => $tax_amount,
            'total_price' => $total_price,
            'delivery_options' => $delivery_options,
            'total_cost_obj' => [
                'unit_price' => $unit_price,
                'quantity' => $quantity,
                'carbon_offset_cost' => $carbon_offset_cost,
                'profit' => $profit,
            ]
        ];
    }

}
