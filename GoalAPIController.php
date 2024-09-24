<?php

namespace App\Http\Controllers\API\V1;

use App\Actions\FinishGoalContract;
use App\Actions\SendOnVoteContract;
use App\Http\Controllers\API\BaseAPIController;
use App\Http\Resources\API\V1\GoalResource;
use App\Http\Resources\API\V1\VoteItemResource;
use App\Models\Goal;
use App\Models\User;
use App\Repositories\GoalRepository;
use Illuminate\Http\Request;

class GoalAPIController extends BaseAPIController
{
    public function __construct(
        private readonly GoalRepository $goalRepository,
    ) {
    }

    /**
     * Display a listing of the resource.
     *
     * @return array|\Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
     */
    public function index(Request $request)
    {
        $user = $request->user();

        return $this->sendResponse(GoalResource::collection($user->goals()->paginate()), __('api/v1/messages/goals.index.success'));
    }

    /**
     * Display a listing of the archived goals.
     *
     * @param  Request  $request
     * @return array|\Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
     */
    public function archive(Request $request)
    {
        $user = $request->user();

        return $this->sendResponse(GoalResource::collection($user->goals()->archived()->paginate()), __('api/v1/messages/goals.archive.success'));
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Goal $goal)
    {
        return $this->sendResponse(GoalResource::make($goal->load(['progress', 'boosters'])), __('api/v1/messages/goals.show.success'));
    }

    /**
     * @param Request $request
     * @return array|\Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
     */
    public function search(Request $request)
    {
        /** @var User $user */
        $user = $request->user();
        $searchQuery = $request->get('q') ?? '';

        return $this->sendResponse(GoalResource::collection(Goal::search($searchQuery)->where('user_id', $user->getKey())->paginate()), __('api/v1/messages/goals.search.success'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Request  $request
     * @param  Goal  $goal
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy(Request $request, Goal $goal)
    {
        $this->goalRepository->delete($goal->getKey());

        return $this->sendResponse(GoalResource::make($goal), __('api/v1/messages/goals.destroy.success'));
    }

    /**
     * Finish the goal by id
     *
     * @param  Request  $request
     * @param  FinishGoalContract  $finishGoalAction
     * @param  Goal  $goal
     * @return \Illuminate\Http\JsonResponse
     */
    public function finish(Request $request, FinishGoalContract $finishGoalAction, Goal $goal)
    {
        $goal = $finishGoalAction->handle($goal);

        return $this->sendResponse(GoalResource::make($goal), __('api/v1/messages/goals.finish.success'));
    }

    /**
     * @param  Request  $request
     * @param  SendOnVoteContract  $sendOnVoteAction
     * @param  Goal  $goal
     * @return array|\Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\JsonResource
     */
    public function sendOnVote(Request $request, SendOnVoteContract $sendOnVoteAction, Goal $goal)
    {
        $goal = $sendOnVoteAction->handle($goal);

        return $this->sendResponse(GoalResource::make($goal), __('api/v1/messages/goals.send_on_vote.success'))->additional([
            'additional' => [
                'vote_items' => VoteItemResource::collection($request->user()->voteItems),
            ]
        ]);
    }
}
