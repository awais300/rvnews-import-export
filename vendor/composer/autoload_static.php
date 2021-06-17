<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit46c0d916ab83132feaf51ad115c7499b
{
    public static $files = array (
        '9e4824c5afbdc1482b6025ce3d4dfde8' => __DIR__ . '/..' . '/league/csv/src/functions_include.php',
    );

    public static $prefixLengthsPsr4 = array (
        'L' => 
        array (
            'League\\Csv\\' => 11,
        ),
        'E' => 
        array (
            'EWA\\RvnewsImportExport\\' => 23,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'League\\Csv\\' => 
        array (
            0 => __DIR__ . '/..' . '/league/csv/src',
        ),
        'EWA\\RvnewsImportExport\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
        'EWA\\RvnewsImportExport\\Company' => __DIR__ . '/../..' . '/src/Company.php',
        'EWA\\RvnewsImportExport\\Customization' => __DIR__ . '/../..' . '/src/Customization.php',
        'EWA\\RvnewsImportExport\\FileUpload' => __DIR__ . '/../..' . '/src/FileUpload.php',
        'EWA\\RvnewsImportExport\\Helper' => __DIR__ . '/../..' . '/src/Helper.php',
        'EWA\\RvnewsImportExport\\ImportExport' => __DIR__ . '/../..' . '/src/ImportExport.php',
        'EWA\\RvnewsImportExport\\Logger' => __DIR__ . '/../..' . '/src/Logger.php',
        'EWA\\RvnewsImportExport\\Product' => __DIR__ . '/../..' . '/src/Product.php',
        'EWA\\RvnewsImportExport\\TemplateLoader' => __DIR__ . '/../..' . '/src/TemplateLoader.php',
        'League\\Csv\\AbstractCsv' => __DIR__ . '/..' . '/league/csv/src/AbstractCsv.php',
        'League\\Csv\\ByteSequence' => __DIR__ . '/..' . '/league/csv/src/ByteSequence.php',
        'League\\Csv\\CannotInsertRecord' => __DIR__ . '/..' . '/league/csv/src/CannotInsertRecord.php',
        'League\\Csv\\CharsetConverter' => __DIR__ . '/..' . '/league/csv/src/CharsetConverter.php',
        'League\\Csv\\ColumnConsistency' => __DIR__ . '/..' . '/league/csv/src/ColumnConsistency.php',
        'League\\Csv\\EncloseField' => __DIR__ . '/..' . '/league/csv/src/EncloseField.php',
        'League\\Csv\\EscapeFormula' => __DIR__ . '/..' . '/league/csv/src/EscapeFormula.php',
        'League\\Csv\\Exception' => __DIR__ . '/..' . '/league/csv/src/Exception.php',
        'League\\Csv\\HTMLConverter' => __DIR__ . '/..' . '/league/csv/src/HTMLConverter.php',
        'League\\Csv\\InvalidArgument' => __DIR__ . '/..' . '/league/csv/src/InvalidArgument.php',
        'League\\Csv\\MapIterator' => __DIR__ . '/..' . '/league/csv/src/MapIterator.php',
        'League\\Csv\\Polyfill\\EmptyEscapeParser' => __DIR__ . '/..' . '/league/csv/src/Polyfill/EmptyEscapeParser.php',
        'League\\Csv\\RFC4180Field' => __DIR__ . '/..' . '/league/csv/src/RFC4180Field.php',
        'League\\Csv\\Reader' => __DIR__ . '/..' . '/league/csv/src/Reader.php',
        'League\\Csv\\ResultSet' => __DIR__ . '/..' . '/league/csv/src/ResultSet.php',
        'League\\Csv\\Statement' => __DIR__ . '/..' . '/league/csv/src/Statement.php',
        'League\\Csv\\Stream' => __DIR__ . '/..' . '/league/csv/src/Stream.php',
        'League\\Csv\\SyntaxError' => __DIR__ . '/..' . '/league/csv/src/SyntaxError.php',
        'League\\Csv\\UnavailableFeature' => __DIR__ . '/..' . '/league/csv/src/UnavailableFeature.php',
        'League\\Csv\\Writer' => __DIR__ . '/..' . '/league/csv/src/Writer.php',
        'League\\Csv\\XMLConverter' => __DIR__ . '/..' . '/league/csv/src/XMLConverter.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit46c0d916ab83132feaf51ad115c7499b::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit46c0d916ab83132feaf51ad115c7499b::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit46c0d916ab83132feaf51ad115c7499b::$classMap;

        }, null, ClassLoader::class);
    }
}
