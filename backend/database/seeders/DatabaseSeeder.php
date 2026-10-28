<?php

namespace Database\Seeders;

use App\Models\BlogLike;
use App\Models\BlogPost;
use App\Models\DictItems;
use App\Models\DictLists;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Пользователи
        $admins = User::factory(2)->admin()->create();

        $moderators = User::factory(1)->moderator()->create();

        $regularUsers = User::factory(7)->create();

        $users = $admins
            ->concat($moderators)
            ->concat($regularUsers);

        // списки с элементами
        foreach ($users as $user) {
            $favorites = DictLists::factory()->create([
                'user_id' => $user->id,
                'type' => 'favorites',
                'name' => 'Избранное',
                'source_lang' => null,
                'target_lang' => null,
                'icon' => 'heart',
            ]);

            $history = DictLists::factory()->create([
                'user_id' => $user->id,
                'type' => 'history',
                'name' => 'История',
                'source_lang' => null,
                'target_lang' => null,
                'icon' => 'history',
            ]);

            $customList = DictLists::factory()->create([
                'user_id' => $user->id,
                'type' => 'custom',
            ]);

            foreach ([$favorites, $history, $customList] as $list) {
                DictItems::factory(5)->create([
                    'list_id' => $list->id,
                ]);
            }
        }

        // Посты
        $posts = BlogPost::factory(20)
            ->state(fn () => [
                'author_id' => $admins->random()->id,
            ])
            ->create();


        // Лайки
        $possibleLikes = $users->flatMap(
            fn ($user) => $posts->map(
                fn ($post) => [
                    'user_id' => $user->id,
                    'post_id' => $post->id,
                ]
            )
        );

        $selectedLikes = $possibleLikes
            ->shuffle()
            ->take(50);

        foreach ($selectedLikes as $like) {
            BlogLike::factory()->create($like);
        }
    }
}