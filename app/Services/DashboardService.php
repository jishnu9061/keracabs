<?php

namespace App\Services;

use App\Models\Blog;
use App\Models\Contact;
use App\Models\Booking;

class DashboardService
{
    public function getDashboardCounts()
    {
        return [
            'blogs' => Blog::count(),
            'contacts' => Contact::count(),
            'bookings' => Booking::count()
        ];
    }
}
