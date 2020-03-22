<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

use App\Product;

use Illuminate\Validation\Rule;

class UpdateProduct extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'slug' => Rule::unique('products')->ignore($this->id), //need to add validation
            'category_id' => 'required | numeric',
            'name' => 'required',
            'price' => 'numeric | nullable',
            'description' => 'string | nullable',
            'image' => 'mimes:jpg,jpeg,png | image | nullable'
        ];
    }
    
    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {   
        $this->merge([
            'slug' => $this->createSlug(),
        ]);
    }

    /**
     * Create slug from name and check if it exists in database
     */
    protected function createSlug() {
        //Ja ir produkta id un slug nav null
        if ( $this->id && $this->slug != null) {
            // Tad izmantojam pieprasījuma slug
            $slug = $this->slug;
        } else {
            // Ja nav produkta id
            // Izveidojam slugu no pieprasījuma vārda
            $slug = Str::slug($this->name, '-');
        }
        // Check if slug exists in database
        $product = Product::where('slug', $slug)->first();
        if($product) {
            if ($product->id != $this->id ) {
                //Change slug
                $slug = $slug . mt_rand( 0, time() );
            }
        }
        return $slug;
    }
}
