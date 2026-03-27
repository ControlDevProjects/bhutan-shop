<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller {
    public function index() {
        $s = Setting::allAsArray();
        return view('admin.settings.index', compact('s'));
    }

    public function update(Request $request) {
        $request->validate([
            'company_name'         => 'required|string|max:120',
            'company_email'        => 'nullable|email|max:120',
            'company_phone'        => 'nullable|string|max:40',
            'company_address'      => 'nullable|string|max:300',
            'company_tagline'      => 'nullable|string|max:200',
            'company_gstin'        => 'nullable|string|max:60',
            'gst_percentage'       => 'required|numeric|min:0|max:100',
            'gst_label'            => 'required|string|max:20',
            'gst_inclusive'        => 'nullable',
            'shipping_default_cost'=> 'required|numeric|min:0',
            'shipping_free_above'  => 'required|numeric|min:0',
            'shipping_express_cost'=> 'required|numeric|min:0',
            'currency_symbol'      => 'required|string|max:10',
            'invoice_footer'       => 'nullable|string|max:500',
        ]);

        $fields = [
            'company_name','company_tagline','company_email','company_phone',
            'company_address','company_gstin','gst_percentage','gst_label',
            'shipping_default_cost','shipping_free_above','shipping_express_cost',
            'currency_symbol','invoice_footer',
        ];
        foreach ($fields as $field) {
            Setting::set($field, $request->input($field, ''));
        }
        // Checkbox
        Setting::set('gst_inclusive', $request->has('gst_inclusive') ? '1' : '0');

        return back()->with('success', 'Settings saved successfully.');
    }
}
