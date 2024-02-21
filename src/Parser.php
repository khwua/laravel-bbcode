<?php

namespace Khwua\BBCode;

class Parser
{
    /**
     * Enabled BBCodes to parse.
     *
     * @var array
     */
    private array $enabledTags;

    /**
     * All loaded BBCodes form config array.
     *
     * @var array
     */
    private array $tags;

    /**
     * Parser constructor.
     *
     * @param array  $tags
     */
    public function __construct(array $tags)
    {
        $this->tags = $tags;
        $this->enabledTags = $this->tags;
    }

    /**
     * Parses the BBCode string
     *
     * @param string  $source
     * @param bool  $caseInsensitive
     *
     * @return string
     */
    public function parse(string $source, bool $caseInsensitive = false): string
    {
        foreach ($this->enabledTags as $name => $parser) {
            $pattern = ($caseInsensitive) ? $parser['pattern'] . 'i' : $parser['pattern'];

            $source = $this->searchAndReplace($pattern, $parser['replace'], $source);
        }

        return $source;
    }

    /**
     * strip all BBCode tags
     *
     * @param string  $source
     *
     * @return string
     */
    public function stripTags(string $source): string
    {
        foreach ($this->tags as $name => $parser) {
            $source = $this->searchAndReplace($parser['pattern'] . 'i', $parser['content'], $source);
        }

        return $source;
    }

    /**
     * Searches after a specified pattern and replaces it with provided structure
     *
     * @param string  $pattern
     * @param string  $replace
     * @param string  $source
     *
     * @return string
     */
    protected function searchAndReplace(string $pattern, string $replace, string $source): string
    {
        while (preg_match($pattern, $source)) {
            $source = preg_replace($pattern, $replace, $source);
        }

        return $source;
    }

    /**
     * Helper function to parse case sensitive
     *
     * @param string  $source
     *
     * @return string
     */
    public function parseCaseSensitive(string $source): string
    {
        return $this->parse($source);
    }

    /**
     * Helper function to parse case insensitive
     *
     * @param string  $source
     *
     * @return string
     */
    public function parseCaseInsensitive(string $source): string
    {
        return $this->parse($source, true);
    }

    /**
     * Limits the parsers to only those you specify
     *
     * @param string|array  $tag
     *
     * @return self
     */
    public function only(string|array $tag): self
    {
        $only = (is_array($tag)) ? $tag : func_get_args();
        $this->enabledTags = array_intersect_key($this->tags, array_flip((array)$only));

        return $this;
    }

    /**
     * Parse all tags except
     *
     * @param string|array  $tag
     *
     * @return self
     */
    public function except(string|array $tag): self
    {
        $except = (is_array($tag)) ? $tag : func_get_args();

        $this->enabledTags = array_diff_key($this->tags, array_flip((array)$except));

        return $this;
    }

    /**
     * List of chosen parsers
     *
     * @return array
     */
    public function getAllBBCodes(): array
    {
        return $this->enabledTags;
    }

    /**
     * Sets the parser pattern and replace pattern.
     * This can be used for new parsers or overwriting existing ones.
     *
     * @param string  $name
     * @param string  $search
     * @param string  $replace
     * @param string  $content
     *
     * @return self
     */
    public function addTag(string $name, string $search, string $replace, string $content): self
    {
        $this->tags[$name] = compact('search', 'replace', 'content');
        $this->enabledTags[$name] = $this->tags[$name];

        return $this;
    }
}
