<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use DB;

class StatisticsController extends Controller
{
    // ki végezte el a legtöbb feladatot
    public function GetWorkerActivity(Request $request){

        try {
            $request->validate([
                'startDate' => 'required|date',
                'endDate' => 'required|date|after:startDate'
            ]);
        } catch (ValidationException $e) {
            return response()->json(["success" => false,'error' => $e->getMessage()], 400);
        }
        

        $results = DB::select('
            SELECT u.name, COUNT(t.id) as task_count
            FROM tasks t
            JOIN users u ON t.worker = u.id
            WHERE t.state = 2
              AND t.state2Date BETWEEN ? AND ?
            GROUP BY t.worker
            ORDER BY task_count DESC
        ', [$request->startDate, $request->endDate]);

        return response()->json(["success" => true,'data' => $results], 200);
    }

    // össz munkaóra 
    public function GetWorkerActivityByTime(Request $request)
    {
        try {
            $request->validate([
                'startDate' => 'required|date',
                'endDate' => 'required|date|after:startDate'
            ]);
        } catch (ValidationException $e) {
            return response()->json(["success" => false, 'error' => $e->getMessage()], 400);
        }

        // Query to calculate the total work time for each worker
        $results = DB::select('
            SELECT u.name, SUM(TIMESTAMPDIFF(SECOND, t.state1date, t.state2date)) AS total_work_time
            FROM tasks t
            JOIN users u ON t.worker = u.id
            WHERE t.state = 2
              AND t.state1date IS NOT NULL
              AND t.state2date IS NOT NULL
              AND t.state2Date BETWEEN ? AND ?
            GROUP BY t.worker
            ORDER BY total_work_time DESC
        ', [$request->startDate, $request->endDate]);

        return response()->json(["success" => true, 'data' => $results], 200);
    }

    // Átlag feladatvégrahajtási idő per munkás (melyik munkás menniy idő alatt hajtotta végre a feladatait?)
    public function GetAverageCompletionTime(Request $request){

        try {
            $request->validate([
                'startDate' => 'required|date',
                'endDate' => 'required|date|after:startDate'
            ]);
        } catch (ValidationException $e) {
            return response()->json(["success" => false,'error' => $e->getMessage()], 400);
        }
        

        $results = DB::select('
            SELECT u.name, AVG(TIMESTAMPDIFF(SECOND, t.state1Date, t.state2Date)) AS avg_completion_time
            FROM tasks t
            JOIN users u ON t.worker = u.id
            WHERE t.state = 2
              AND t.state2Date BETWEEN ? AND ?
            GROUP BY t.worker
            ORDER BY avg_completion_time ASC
        ', [$request->startDate, $request->endDate]);

        return response()->json(["success" => true,'data' => $results], 200);
    }

    // Átlag feladatvégrahajtási idő per feladattípus
    public function GetAverageTaskCompletionTime(Request $request)
    {
        try {
            $request->validate([
                'startDate' => 'required|date',
                'endDate' => 'required|date|after:startDate',
                'task_type' => 'required|integer|exists:task_types,id'
            ]);
        } catch (ValidationException $e) {
            return response()->json(["success" => false, 'error' => $e->getMessage()], 400);
        }

        // Query to calculate the average task completion time for the given taskType
        $results = DB::select('
            SELECT AVG(TIMESTAMPDIFF(SECOND, t.state1date, t.state2date)) AS avg_task_completion_time
            FROM tasks t
            WHERE t.task_type = ?
              AND t.state = 2
              AND t.state1date IS NOT NULL
              AND t.state2date IS NOT NULL
              AND t.state2Date BETWEEN ? AND ?
        ', [$request->task_type, $request->startDate, $request->endDate]);

        return response()->json(["success" => true, 'data' => $results], 200);
    }

    // Mely osztály mennyi feladatot végzett el
    public function GetTaskCountByRole(Request $request){

        try {
            $request->validate([
                'startDate' => 'required|date',
                'endDate' => 'required|date|after:startDate'
            ]);
        } catch (ValidationException $e) {
            return response()->json(["success" => false,'error' => $e->getMessage()], 400);
        }
        

        $results = DB::select('
            SELECT r.role_name, COUNT(t.id) AS task_count
            FROM tasks t
            JOIN users u ON t.worker = u.id
            JOIN roles r ON u.role = r.id
            WHERE t.state2Date BETWEEN ? AND ?
            GROUP BY r.role_name
            ORDER BY task_count DESC
        ', [$request->startDate, $request->endDate]);

        return response()->json(["success" => true,'data' => $results], 200);
    }

    // ki használta a legtöbbet a rendszert? (nem feladat végrehajtás hanem pl. fálj le/fel töltés)
    public function GetMostActiveApiUsers(Request $request)
    {
        try {
            $request->validate([
                'startDate' => 'required|date',
                'endDate' => 'required|date|after:startDate'
            ]);
        } catch (ValidationException $e) {
            return response()->json(["success" => false, 'error' => $e->getMessage()], 400);
        }

        // Query to get users with the most API usage
        $results = DB::select('
            SELECT u.id, u.name, COUNT(l.id) AS api_usage_count
            FROM api_access_logs l
            JOIN users u ON l.user_id = u.id
            WHERE l.created_at BETWEEN ? AND ?
            GROUP BY u.id, u.name
            ORDER BY api_usage_count DESC
        ', [$request->startDate, $request->endDate]);

        return response()->json(["success" => true, 'data' => $results], 200);
    }

    // hány request történt a kijelölt időszakban
    public function GetApiUsage(Request $request)
    {
        try {
            $request->validate([
                'startDate' => 'required|date',
                'endDate' => 'required|date|after:startDate'
            ]);
        } catch (ValidationException $e) {
            return response()->json(["success" => false, 'error' => $e->getMessage()], 400);
        }

        $results = DB::select('
            SELECT COUNT(api_access_logs.id) AS count
            FROM api_access_logs
            WHERE api_access_logs.created_at BETWEEN ? AND ?
        ', [$request->startDate, $request->endDate]);

        return response()->json(["success" => true, 'data' => $results], 200);
    }

    //melyik útvonalakat használtuk a legtöbbet?
    public function GetMostRequestedRoute(Request $request)
    {
        try {
            $request->validate([
                'startDate' => 'required|date',
                'endDate' => 'required|date|after:startDate'
            ]);
        } catch (ValidationException $e) {
            return response()->json(["success" => false, 'error' => $e->getMessage()], 400);
        }

        // Query to get the most requested route within the given date range
        $results = DB::select('
            SELECT route, COUNT(id) AS request_count
            FROM api_access_logs
            WHERE created_at BETWEEN ? AND ?
            GROUP BY route
            ORDER BY request_count DESC
        ', [$request->startDate, $request->endDate]);

        return response()->json(["success" => true, 'data' => $results], 200);
    }

    


}
