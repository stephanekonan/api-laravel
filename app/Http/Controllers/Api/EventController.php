<?php

namespace App\Http\Controllers\Api;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::query()->latest()->get();

        return response()->json([
            'success' => true,
            'data' => $events,
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'date' => 'required|date',
            'lieu' => 'required|string|max:255',
            'file' => 'nullable|file|mimes:jpeg,png,jpg|max:51200',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $file = $request->file('file');
        $imageUrl = null;

        if ($file) {
            $filename = uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images/events'), $filename);
            $imageUrl = 'images/events/' . $filename;
        }

        $event = Event::create([
            'title' => $request->title,
            'description' => $request->description,
            'date' => $request->date,
            'lieu' => $request->lieu,
            'url_image' => $imageUrl,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Événement créé avec succès.',
            'data' => $event,
        ], 201);
    }

    public function edit($id)
    {
        $event = Event::find($id);

        if (!$event) {
            return response()->json([
                'success' => false,
                'message' => 'Événement introuvable.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $event,
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $event = Event::find($id);

        if (!$event) {
            return response()->json([
                'success' => false,
                'message' => 'Événement introuvable.',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'date' => 'required|date',
            'lieu' => 'required|string|max:255',
            'file' => 'nullable|file|mimes:jpeg,png,jpg|max:51200',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $file = $request->file('file');
        $imageUrl = $event->url_image;

        if ($file) {
            $filename = uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images/events'), $filename);
            $imageUrl = 'images/events/' . $filename;
        }

        $event->update([
            'title' => $request->title,
            'description' => $request->description,
            'date' => $request->date,
            'lieu' => $request->lieu,
            'url_image' => $imageUrl,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Événement mis à jour avec succès.',
            'data' => $event,
        ], 200);
    }

    public function delete($id)
    {
        $event = Event::find($id);

        if (!$event) {
            return response()->json([
                'success' => false,
                'message' => 'Événement introuvable.',
            ], 404);
        }

        if ($event->url_image && file_exists(public_path($event->url_image))) {
            unlink(public_path($event->url_image));
        }

        $event->delete();

        return response()->json([
            'success' => true,
            'message' => 'Événement supprimé avec succès.',
        ], 200);
    }
}
