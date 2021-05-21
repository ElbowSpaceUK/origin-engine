<?php

namespace App\Core\Helpers\Composer\Schema\Schema;

use Illuminate\Contracts\Support\Arrayable;

class SupportSchema implements Arrayable
{

    /**
     * @var string|null
     */
    private ?string $email;

    /**
     * @var string|null
     */
    private ?string $issues;

    /**
     * @var string|null
     */
    private ?string $forum;

    /**
     * @var string|null
     */
    private ?string $wiki;

    /**
     * @var string|null
     */
    private ?string $irc;

    /**
     * @var string|null
     */
    private ?string $source;

    /**
     * @var string|null
     */
    private ?string $docs;

    /**
     * @var string|null
     */
    private ?string $rss;

    /**
     * @var string|null
     */
    private ?string $chat;

    /**
     * SupportSchema constructor.
     * @param string|null $email
     * @param string|null $issues
     * @param string|null $forum
     * @param string|null $wiki
     * @param string|null $irc
     * @param string|null $source
     * @param string|null $docs
     * @param string|null $rss
     * @param string|null $chat
     */
    public function __construct(?string $email = null,
                                ?string $issues = null,
                                ?string $forum = null,
                                ?string $wiki = null,
                                ?string $irc = null,
                                ?string $source = null,
                                ?string $docs = null,
                                ?string $rss = null,
                                ?string $chat = null)
    {
        $this->email = $email;
        $this->issues = $issues;
        $this->forum = $forum;
        $this->wiki = $wiki;
        $this->irc = $irc;
        $this->source = $source;
        $this->docs = $docs;
        $this->rss = $rss;
        $this->chat = $chat;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string|null $email
     */
    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string|null
     */
    public function getIssues(): ?string
    {
        return $this->issues;
    }

    /**
     * @param string|null $issues
     */
    public function setIssues(?string $issues): void
    {
        $this->issues = $issues;
    }

    /**
     * @return string|null
     */
    public function getForum(): ?string
    {
        return $this->forum;
    }

    /**
     * @param string|null $forum
     */
    public function setForum(?string $forum): void
    {
        $this->forum = $forum;
    }

    /**
     * @return string|null
     */
    public function getWiki(): ?string
    {
        return $this->wiki;
    }

    /**
     * @param string|null $wiki
     */
    public function setWiki(?string $wiki): void
    {
        $this->wiki = $wiki;
    }

    /**
     * @return string|null
     */
    public function getIrc(): ?string
    {
        return $this->irc;
    }

    /**
     * @param string|null $irc
     */
    public function setIrc(?string $irc): void
    {
        $this->irc = $irc;
    }

    /**
     * @return string|null
     */
    public function getSource(): ?string
    {
        return $this->source;
    }

    /**
     * @param string|null $source
     */
    public function setSource(?string $source): void
    {
        $this->source = $source;
    }

    /**
     * @return string|null
     */
    public function getDocs(): ?string
    {
        return $this->docs;
    }

    /**
     * @param string|null $docs
     */
    public function setDocs(?string $docs): void
    {
        $this->docs = $docs;
    }

    /**
     * @return string|null
     */
    public function getRss(): ?string
    {
        return $this->rss;
    }

    /**
     * @param string|null $rss
     */
    public function setRss(?string $rss): void
    {
        $this->rss = $rss;
    }

    /**
     * @return string|null
     */
    public function getChat(): ?string
    {
        return $this->chat;
    }

    /**
     * @param string|null $chat
     */
    public function setChat(?string $chat): void
    {
        $this->chat = $chat;
    }


    public function toArray()
    {
        return collect([
            'email' => $this->email,
            'issues' => $this->issues,
            'forum' => $this->forum,
            'wiki' => $this->wiki,
            'irc' => $this->irc,
            'source' => $this->source,
            'docs' => $this->docs,
            'rss' => $this->rss,
            'chat' => $this->chat,
        ])->filter(fn($val) => $val !== [] && $val !== null && ($val instanceof Collection ? $val->count() > 0 : true))->toArray();
    }
}
