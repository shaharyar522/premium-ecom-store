<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CouponController extends Controller
{
    public function index()
    {
        return view('admin.coupons.index', ['coupons' => Coupon::latest()->get()]);
    }

    public function create()
    {
        return view('admin.coupons.form', ['coupon' => new Coupon(['type' => 'percent', 'is_active' => true])]);
    }

    public function store(Request $request)
    {
        Coupon::create($this->data($request));

        return redirect()->route('admin.coupons.index')->with('ok', 'Coupon created.');
    }

    public function edit(Coupon $coupon)
    {
        return view('admin.coupons.form', ['coupon' => $coupon]);
    }

    public function update(Request $request, Coupon $coupon)
    {
        $coupon->update($this->data($request, $coupon));

        return redirect()->route('admin.coupons.index')->with('ok', 'Coupon saved.');
    }

    public function destroy(Coupon $coupon)
    {
        $coupon->delete();

        return back()->with('ok', 'Coupon deleted.');
    }

    private function data(Request $request, ?Coupon $coupon = null): array
    {
        $request->merge(['code' => strtoupper(trim((string) $request->input('code')))]);
        $data = $request->validate([
            'code' => ['required', 'alpha_dash', 'max:40', Rule::unique('coupons', 'code')->ignore($coupon?->id)],
            'type' => 'required|in:percent,fixed',
            'value' => ['required', 'integer', 'min:1', $request->input('type') === 'percent' ? 'max:100' : 'max:10000000'],
            'min_subtotal' => 'nullable|integer|min:0',
            'max_uses' => 'nullable|integer|min:1',
            'expires_at' => 'nullable|date',
        ]);
        $data['min_subtotal'] = (int) ($data['min_subtotal'] ?? 0);
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
