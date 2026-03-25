<?php

declare(strict_types=1);

namespace Database\Factories\Modules\PublicService\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\PublicService\Models\Announcement;
use App\Models\User;
use Illuminate\Support\Str;

class AnnouncementFactory extends Factory
{
    protected $model = Announcement::class;

    public function definition(): array
    {
        $title = $this->faker->sentence();
        return [
            'id' => $this->faker->uuid(),
            'title' => $title,
            'slug' => Str::slug($title),
            'content' => $this->faker->paragraphs(3, true),
            'is_published' => true,
            'author_id' => User::factory(),
        ];
    }
}
