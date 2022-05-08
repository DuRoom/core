<?php

/*
 * This file is part of DuRoom.
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace DuRoom\Tests\integration\extenders;

use DuRoom\Extend\Frontend;
use DuRoom\Frontend\Document;
use DuRoom\Frontend\Driver\TitleDriverInterface;
use DuRoom\Testing\integration\RetrievesAuthorizedUsers;
use DuRoom\Testing\integration\TestCase;
use Psr\Http\Message\ServerRequestInterface;

class FrontendTitleTest extends TestCase
{
    use RetrievesAuthorizedUsers;

    protected function setUp(): void
    {
        $this->prepareDatabase([
            'users' => [
                $this->normalUser(),
            ],
            'discussions' => [
                ['id' => 1, 'title' => 'Test Discussion', 'user_id' => 1, 'first_post_id' => 1]
            ],
            'posts' => [
                ['id' => 1, 'discussion_id' => 1, 'user_id' => 2, 'type' => 'comment', 'content' => '<t><p>can i haz potat?</p></t>'],
            ],
        ]);

        $this->setting('forum_title', 'DuRoom');
    }

    /**
     * @test
     */
    public function basic_title_driver_is_used_by_default()
    {
        $this->assertTitleEquals('Test Discussion - DuRoom');
    }

    /**
     * @test
     */
    public function custom_title_driver_works_if_set()
    {
        $this->extend((new Frontend('forum'))->title(CustomTitleDriver::class));

        $this->assertTitleEquals('CustomTitle');
    }

    private function assertTitleEquals(string $title): void
    {
        $response = $this->send($this->request('GET', '/d/1'));

        preg_match('/\<title\>(?<title>[^<]+)\<\/title\>/m', $response->getBody()->getContents(), $matches);

        $this->assertEquals($title, $matches['title']);
    }
}

class CustomTitleDriver implements TitleDriverInterface
{
    public function makeTitle(Document $document, ServerRequestInterface $request, array $forumApiDocument): string
    {
        return 'CustomTitle';
    }
}