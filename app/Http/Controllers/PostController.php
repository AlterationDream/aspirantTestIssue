<?php

namespace App\Http\Controllers;

use Illuminate\Container\Container;
use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Vedmant\FeedReader\FeedReader;
use Vedmant\FeedReader\FeedReaderServiceProvider;
use Vedmant\FeedReader\Tests\Unit\FeedReaderTest;

class PostController extends Controller
{
    public function index() {
        $posts = Post::orderBy('pubDate', 'asc')->take(10)->get();
        return view('posts.index', ['posts' => $posts]);
    }

    public function show($id) {
        $post = Post::find($id);
        if (!$post) abort(404);
        return view('posts.show', ['post' => $post]);
    }

    /**
     * Import iTunes Movie Trailers from RSS.
     * <code>
     * $result = [
     *      'status' => (string) 'success|error',
     *      'error' => 'message' (optional)
     * ];
     * </code>
     *
     * @param integer $amount
     * @param bool $fresh
     *
     * @return string[]
     */
    public function import($amount, $fresh) {
        $feedReader = new FeedReader(Container::getInstance());

        $feed = $feedReader->read('https://trailers.apple.com/trailers/home/rss/newtrailers.rss');
        if ($feed->error()) {
            return [
                'status' => 'error',
                'error' => 'An error occurred when connecting to an RSS feed.'
            ];
        }

        $items = $feed->get_items(0, $amount);
        if (count($items) == 0) {
            return [
                'status' => 'error',
                'error' => 'An error occurred when collecting items from RSS feed after a successful connection.'
            ];
        }

        if ($fresh) {
            try {
                $this->wipeDB();
            } catch (\Illuminate\Database\QueryException $exception) {
                return [
                    'status' => 'error',
                    'error' => 'A database error occurred when trying to wipe previous records. ' .
                        PHP_EOL . PHP_EOL . $exception->getMessage()
                ];
            }
        }

        $successfulRecords = array();
        foreach ($items as $item) {
            try {

                $successfulRecords[] = Post::create([
                    'title' => $item->get_title(),
                    'description' => $item->get_description(),
                    'image' => $item->get_link() . '/images/background.jpg',
                    'link' => $item->get_link(),
                    'pubDate' => \Carbon\Carbon::parse($items[0]->get_date())->format('Y-m-d H:i:s')
                ]);

            } catch (\Illuminate\Database\QueryException $exception) {

                $success = $this->revertQuery($successfulRecords);
                return [
                    'status' => 'error',
                    'error' => 'An error occurred when creating a database post record. ' .
                        (($success) ? 'Any successful records were reverted.' :
                        'Query failed to revert. A database error occurred.') .
                        PHP_EOL . PHP_EOL . $exception->getMessage()
                    ];

            }
        }

        return ['status' => 'success'];
    }

    /**
     * Wipe posts database.
     *
     * @return void
     * */
    public function wipeDB() {
        $posts = Post::all();
        foreach ($posts as $post) {
            $post->likedUsers()->detach();
        }
        Post::truncate();
    }

    /**
     * Revert database query on a failed post import.
     *
     * @param array $successfulRecords
     *
     * @return bool
     * */
    public function revertQuery($successfulRecords) {
        foreach ($successfulRecords as $record) {
            if ($record instanceof Post) {
                try {
                    $record->delete();
                } catch (\Illuminate\Database\QueryException $exception) {
                    return false;
                }
            }
        }
        return true;
    }

    public function like($id, Request $request) {
        if (!\Auth::check()) {
            $response['status'] = 'error';
            $response['msg'] = 'Bad login.';
        }

        $post = Post::find($id);
        if (!$post) {
            $response['status'] = 'error';
            $response['msg'] = 'Post not found.';
        }

        $hasLike = $post->likedUsers()->find(\Auth::user()->id);
        if (!$hasLike) {
            $post->likedUsers()->attach(\Auth::user()->id);
            $response['status'] = 'success';
            $response['newState'] = 1;
            $response['count'] = $post->likedUsers()->count();
            return json_encode($response);
        }

        $post->likedUsers()->detach(\Auth::user()->id);
        $response['status'] = 'success';
        $response['newState'] = 0;
        $response['count'] = $post->likedUsers()->count();
        return json_encode($response);
    }
}
