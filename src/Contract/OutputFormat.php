<?php

namespace OneToMany\PdfToImage\Contract;

enum OutputFormat: string
{

    case Jpeg = 'jpeg';
    case Png = 'png';
    case Tiff = 'tiff';

}
