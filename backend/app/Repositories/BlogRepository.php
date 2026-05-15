<?php

namespace App\Repositories;

use App\Models\Blog;

class BlogRepository
{
    public function index($request)
    {
        $query = Blog::with('park');

        // USER chỉ xem bài active
        if (!$request->isAdmin) {

            $query->where('Status', 1);
        }

        // tìm kiếm title
        if (!empty($request->keyword)) {

            $query->where('Title', 'like', '%' . $request->keyword . '%')
                  ->orWhere('Tags', 'like', '%' . $request->keyword . '%');
        }

        // filter status cho admin
        if ($request->Status !== null && $request->Status !== '') {

            $query->where('Status', $request->Status);
        }

        return $query
            ->orderBy('ID', 'desc')
            ->paginate(10);
    }

    public function find($id)
    {
        return Blog::with('park')->find($id);
    }

    public function store($data)
    {
        return Blog::create($data);
    }

    public function update($id, $data)
    {
        $blog = Blog::find($id);

        if (!$blog) {
            return null;
        }

        $blog->update($data);

        return $blog->fresh();
    }

    public function delete($id)
    {
        $blog = Blog::find($id);

        if (!$blog) {
            return false;
        }

        $blog->delete();

        return true;
    }
}