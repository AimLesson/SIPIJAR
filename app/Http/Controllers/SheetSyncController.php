<?php
namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SheetSyncController extends Controller
{
    // Return data for Google Sheets (GET)
    public function getData()
    {
        $data = Event::all();
        return response()->json($data);
    }
    public function updateData(Request $request)
    {
        try {
            foreach ($request->all() as $row) {
                if (!isset($row['id']))
                    continue;

                // Format ulang field 'date' (tanggal saja)
                if (!empty($row['date'])) {
                    try {
                        $row['date'] = Carbon::parse($row['date'])->format('Y-m-d');
                    } catch (\Exception $e) {
                        unset($row['date']);
                    }
                }

                // Jika ada waktu yang pakai jam (misalnya untuk start_time, finish_time)
                $timeFields = ['start_time', 'finish_time', 'created_at', 'updated_at'];
                foreach ($timeFields as $field) {
                    if (!empty($row[$field])) {
                        try {
                            $row[$field] = Carbon::parse($row[$field])->format('Y-m-d H:i:s');
                        } catch (\Exception $e) {
                            unset($row[$field]);
                        }
                    }
                }

                DB::table('events')->where('id', $row['id'])->update($row);
            }

            return response()->json(['status' => 'updated']);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

}

