<?php
namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SheetSyncController extends Controller
{
    // Return data for Google Sheets (GET)
    public function getData()
    {
        $data = Event::all();
        return response()->json($data);
    }

    // Update data from Google Sheets (POST)
    public function updateData(Request $request)
    {
        // Example: Update based on ID
        foreach ($request->all() as $row) {
            Event::where('id', $row['id'])->update($row);
        }
        return response()->json(['status' => 'success']);
    }
}

