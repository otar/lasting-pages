<?php

namespace App\Services;

use App\DataTransferObjects\ReadabilityDto;
use fivefilters\Readability\Configuration;
use fivefilters\Readability\ParseException;
use fivefilters\Readability\Readability;

class ReadabilityService
{
    /**
     * @throws ParseException
     */
    public function parse(string $html): ReadabilityDto
    {
        $readability = new Readability(new Configuration);

        $readability->parse($html);

        return new ReadabilityDto(
            title: $readability->getTitle() ?? '',
            content: $readability->getContent() ?? '',
            excerpt: $readability->getExcerpt(),
            author: $readability->getAuthor(),
            textDirection: $readability->getDirection(),
        );
    }
}
