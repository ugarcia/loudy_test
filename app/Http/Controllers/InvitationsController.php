<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invitation;
use App\Models\User;

class InvitationsController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $userFrom = $request->user();
        $userTo = User::find($request->input('to_id'));
        if (!$userTo) {
            return response()->json([
                'error' => 'user to invite does not exist'
            ], 400);
        }
        if ($userTo->id === $userFrom->id) {
            return response()->json([
                'error' => 'user cannot self-invite'
            ], 400);            
        }
        $invitation = Invitation::create([
            'from_id' => $userFrom->id,
            'to_id' => $userTo->id,
            'state' => 'created'
        ]);
        return response()->json($invitation, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Invitation::findOrFail($id);
    }

    /**
     * Cancel the invitation.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function cancel(Request $request, $id)
    {
        $userFrom = $request->user();
        $invitation = Invitation::find($id);
        if (!$invitation) {
            return response([
                'error' => 'invitation does not exist'
            ], 400);
        }
        if ($userFrom->id !== $invitation->from_id) {
            return response([
                'error' => 'user did not create this invitation'
            ], 401);
        }
        if ($invitation->state === 'cancelled') {
            return response([
                'error' => 'invitation already cancelled'
            ], 400);
        }
        $invitation->state = 'cancelled';
        $invitation->save();
        return response($invitation, 200);
    }

    /**
     * Accept the invitation.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function accept(Request $request, $id)
    {
        $userFrom = $request->user();
        $invitation = Invitation::find($id);
        if (!$invitation) {
            return response([
                'error' => 'invitation does not exist'
            ], 400);
        }
        if ($userFrom->id !== $invitation->to_id) {
            return response([
                'error' => 'user did not receive this invitation'
            ], 401);
        }
        if ($invitation->state === 'cancelled') {
            return response([
                'error' => 'invitation already cancelled'
            ], 400);
        }
        if ($invitation->state === 'accepted') {
            return response([
                'error' => 'invitation already accepted'
            ], 400);
        }
        $invitation->state = 'accepted';
        $invitation->save();
        return response($invitation, 200);
    }

    /**
     * Reject the invitation.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function reject(Request $request, $id)
    {
        $userFrom = $request->user();
        $invitation = Invitation::find($id);
        if (!$invitation) {
            return response([
                'error' => 'invitation does not exist'
            ], 400);
        }
        if ($userFrom->id !== $invitation->to_id) {
            return response([
                'error' => 'user did not receive this invitation'
            ], 401);
        }
        if ($invitation->state === 'cancelled') {
            return response([
                'error' => 'invitation already cancelled'
            ], 400);
        }
        if ($invitation->state === 'rejected') {
            return response([
                'error' => 'invitation already rejected'
            ], 400);
        }
        $invitation->state = 'rejected';
        $invitation->save();
        return response($invitation, 200);
    }
}
