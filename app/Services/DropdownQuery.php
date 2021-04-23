<?php

namespace App\Services;

use App\Models\VehicleMake;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class DropdownQuery
{
  public static function makes(Request $request)
  {
      $cat = $request->filled('cat_id') ? $request->input('cat_id') : '';
      $make = $request->filled('make') ? $request->input('make') : '';
      return Article::modelsByMake($make, $cat);
  }

  public static function modelsB(Request $request)
  {
      $cat = $request->filled('cat_id') ? $request->input('cat_id') : '';
      $make = $request->filled('make') ? $request->input('make') : '';
      return Article::modelsByMake($make, $cat);
  }
}