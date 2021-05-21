<?php

namespace App\Core\Helpers\Composer\Schema\Schema;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;

class ComposerSchema implements Arrayable
{

    /**
     * @var string
     */
    private string $name;

    /**
     * @var string|null
     */
    private ?string $description;

    /**
     * @var string|null
     */
    private ?string $version;

    /**
     * @var string
     */
    private string $type;

    /**
     * @var array|string[]
     */
    private array $keywords;

    /**
     * @var string|null
     */
    private ?string $homepage;

    /**
     * @var string|null
     */
    private ?string $readme;

    /**
     * @var Carbon|null
     */
    private ?Carbon $time;

    /**
     * @var string|array|null
     */
    private $license;

    /**
     * @var array|AuthorSchema[]
     */
    private array $authors;

    /**
     * @var SupportSchema|null
     */
    private ?SupportSchema $support;

    /**
     * @var array|FundingSchema[]
     */
    private array $funding;

    /**
     * @var array|PackageSchema[]
     */
    private array $require;

    /**
     * @var array|PackageSchema[]
     */
    private array $requireDev;

    /**
     * @var array|PackageSchema[]
     */
    private array $conflict;

    /**
     * @var array|PackageSchema[]
     */
    private array $replace;

    /**
     * @var array|PackageSchema[]
     */
    private array $provide;

    /**
     * @var array|SuggestedPackageSchema[]
     */
    private array $suggest;

    /**
     * @var AutoloadSchema|null
     */
    private ?AutoloadSchema $autoload;

    /**
     * @var AutoloadSchema|null
     */
    private ?AutoloadSchema $autoloadDev;

    /**
     * @var array
     */
    private array $includePath;

    /**
     * @var string|null
     */
    private ?string $targetDir;

    /**
     * @var string|null
     */
    private ?string $minimumStability;

    private bool $preferStable;

    /**
     * @var array|RepositorySchema[]
     */
    private array $repositories;

    /**
     * @var array
     */
    private array $config;

    /**
     * @var array|ScriptSchema[]
     */
    private array $scripts;

    private array $extra;

    /**
     * @var array|string[]
     */
    private array $bin;

    /**
     * @var string|null
     */
    private ?string $archive;

    /**
     * @var bool|string|null
     */
    private $abandoned;

    private array $nonFeatureBranches;

    /**
     * ComposerSchema constructor.
     * @param string $name
     * @param string|null $description
     * @param string|null $version
     * @param string $type
     * @param array|string[] $keywords
     * @param string|null $homepage
     * @param string|null $readme
     * @param Carbon|null $time
     * @param array|string|null $license
     * @param AuthorSchema[]|array $authors
     * @param SupportSchema|null $support
     * @param FundingSchema[]|array $funding
     * @param PackageSchema[]|array $require
     * @param PackageSchema[]|array $requireDev
     * @param PackageSchema[]|array $conflict
     * @param PackageSchema[]|array $replace
     * @param PackageSchema[]|array $provide
     * @param SuggestedPackageSchema[]|array $suggest
     * @param AutoloadSchema|null $autoload
     * @param AutoloadSchema|null $autoloadDev
     * @param array $includePath
     * @param string|null $targetDir
     * @param string|null $minimumStability
     * @param bool $preferStable
     * @param RepositorySchema[]|array $repositories
     * @param array $config
     * @param ScriptSchema[]|array $scripts
     * @param array $extra
     * @param array|string[] $bin
     * @param string|null $archive
     * @param bool|string|null $abandoned
     * @param array $nonFeatureBranches
     */
    public function __construct(string $name,
                                ?string $description = null,
                                ?string $version = null,
                                string $type = 'library',
                                array $keywords = [],
                                ?string $homepage = null,
                                ?string $readme = null,
                                ?Carbon $time = null,
                                $license = null,
                                array $authors = [],
                                ?SupportSchema $support = null,
                                array $funding = [],
                                array $require = [],
                                array $requireDev = [],
                                array $conflict = [],
                                array $replace = [],
                                array $provide = [],
                                array $suggest = [],
                                ?AutoloadSchema $autoload = null,
                                ?AutoloadSchema $autoloadDev = null,
                                array $includePath = [],
                                ?string $targetDir = null,
                                ?string $minimumStability = null,
                                bool $preferStable = false,
                                array $repositories = [],
                                array $config = [],
                                array $scripts = [],
                                array $extra = [],
                                array $bin = [],
                                ?string $archive = null,
                                $abandoned = false,
                                array $nonFeatureBranches = [])
    {
        $this->name = $name;
        $this->description = $description;
        $this->version = $version;
        $this->type = $type;
        $this->keywords = $keywords;
        $this->homepage = $homepage;
        $this->readme = $readme;
        $this->time = $time;
        $this->license = $license;
        $this->authors = $authors;
        $this->support = $support;
        $this->funding = $funding;
        $this->require = $require;
        $this->requireDev = $requireDev;
        $this->conflict = $conflict;
        $this->replace = $replace;
        $this->provide = $provide;
        $this->suggest = $suggest;
        $this->autoload = $autoload;
        $this->autoloadDev = $autoloadDev;
        $this->includePath = $includePath;
        $this->targetDir = $targetDir;
        $this->minimumStability = $minimumStability;
        $this->preferStable = $preferStable;
        $this->repositories = $repositories;
        $this->config = $config;
        $this->scripts = $scripts;
        $this->extra = $extra;
        $this->bin = $bin;
        $this->archive = $archive;
        $this->abandoned = $abandoned;
        $this->nonFeatureBranches = $nonFeatureBranches;
    }


    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return string|null
     */
    public function getVersion(): ?string
    {
        return $this->version;
    }

    /**
     * @param string|null $version
     */
    public function setVersion(?string $version): void
    {
        $this->version = $version;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @return array|string[]
     */
    public function getKeywords(): array
    {
        return $this->keywords;
    }

    /**
     * @param array|string[] $keywords
     */
    public function setKeywords(array $keywords): void
    {
        $this->keywords = $keywords;
    }

    /**
     * @return string|null
     */
    public function getHomepage(): ?string
    {
        return $this->homepage;
    }

    /**
     * @param string|null $homepage
     */
    public function setHomepage(?string $homepage): void
    {
        $this->homepage = $homepage;
    }

    /**
     * @return string|null
     */
    public function getReadme(): ?string
    {
        return $this->readme;
    }

    /**
     * @param string|null $readme
     */
    public function setReadme(?string $readme): void
    {
        $this->readme = $readme;
    }

    /**
     * @return Carbon|null
     */
    public function getTime(): ?Carbon
    {
        return $this->time;
    }

    /**
     * @param Carbon|null $time
     */
    public function setTime(?Carbon $time): void
    {
        $this->time = $time;
    }

    /**
     * @return array|string|null
     */
    public function getLicense()
    {
        return $this->license;
    }

    /**
     * @param array|string|null $license
     */
    public function setLicense($license): void
    {
        $this->license = $license;
    }

    /**
     * @return AuthorSchema[]|array
     */
    public function getAuthors(): array
    {
        return $this->authors;
    }

    /**
     * @param AuthorSchema[]|array $authors
     */
    public function setAuthors(array $authors): void
    {
        $this->authors = $authors;
    }

    /**
     * @return SupportSchema|null
     */
    public function getSupport(): ?SupportSchema
    {
        return $this->support;
    }

    /**
     * @param SupportSchema|null $support
     */
    public function setSupport(?SupportSchema $support): void
    {
        $this->support = $support;
    }

    /**
     * @return FundingSchema[]|array
     */
    public function getFunding(): array
    {
        return $this->funding;
    }

    /**
     * @param FundingSchema[]|array $funding
     */
    public function setFunding(array $funding): void
    {
        $this->funding = $funding;
    }

    /**
     * @return PackageSchema[]|array
     */
    public function getRequire(): array
    {
        return $this->require;
    }

    /**
     * @param PackageSchema[]|array $require
     */
    public function setRequire(array $require): void
    {
        $this->require = $require;
    }

    /**
     * @return PackageSchema[]|array
     */
    public function getRequireDev(): array
    {
        return $this->requireDev;
    }

    /**
     * @param PackageSchema[]|array $requireDev
     */
    public function setRequireDev(array $requireDev): void
    {
        $this->requireDev = $requireDev;
    }

    /**
     * @return PackageSchema[]|array
     */
    public function getConflict(): array
    {
        return $this->conflict;
    }

    /**
     * @param PackageSchema[]|array $conflict
     */
    public function setConflict(array $conflict): void
    {
        $this->conflict = $conflict;
    }

    /**
     * @return PackageSchema[]|array
     */
    public function getReplace(): array
    {
        return $this->replace;
    }

    /**
     * @param PackageSchema[]|array $replace
     */
    public function setReplace(array $replace): void
    {
        $this->replace = $replace;
    }

    /**
     * @return PackageSchema[]|array
     */
    public function getProvide(): array
    {
        return $this->provide;
    }

    /**
     * @param PackageSchema[]|array $provide
     */
    public function setProvide(array $provide): void
    {
        $this->provide = $provide;
    }

    /**
     * @return SuggestedPackageSchema[]|array
     */
    public function getSuggest(): array
    {
        return $this->suggest;
    }

    /**
     * @param SuggestedPackageSchema[]|array $suggest
     */
    public function setSuggest(array $suggest): void
    {
        $this->suggest = $suggest;
    }

    /**
     * @return AutoloadSchema|null
     */
    public function getAutoload(): ?AutoloadSchema
    {
        return $this->autoload;
    }

    /**
     * @param AutoloadSchema|null $autoload
     */
    public function setAutoload(?AutoloadSchema $autoload): void
    {
        $this->autoload = $autoload;
    }

    /**
     * @return AutoloadSchema|null
     */
    public function getAutoloadDev(): ?AutoloadSchema
    {
        return $this->autoloadDev;
    }

    /**
     * @param AutoloadSchema|null $autoloadDev
     */
    public function setAutoloadDev(?AutoloadSchema $autoloadDev): void
    {
        $this->autoloadDev = $autoloadDev;
    }

    /**
     * @return array
     */
    public function getIncludePath(): array
    {
        return $this->includePath;
    }

    /**
     * @param array $includePath
     */
    public function setIncludePath(array $includePath): void
    {
        $this->includePath = $includePath;
    }

    /**
     * @return string|null
     */
    public function getTargetDir(): ?string
    {
        return $this->targetDir;
    }

    /**
     * @param string|null $targetDir
     */
    public function setTargetDir(?string $targetDir): void
    {
        $this->targetDir = $targetDir;
    }

    /**
     * @return string|null
     */
    public function getMinimumStability(): ?string
    {
        return $this->minimumStability;
    }

    /**
     * @param string|null $minimumStability
     */
    public function setMinimumStability(?string $minimumStability): void
    {
        $this->minimumStability = $minimumStability;
    }

    /**
     * @return bool
     */
    public function isPreferStable(): bool
    {
        return $this->preferStable;
    }

    /**
     * @param bool $preferStable
     */
    public function setPreferStable(bool $preferStable): void
    {
        $this->preferStable = $preferStable;
    }

    /**
     * @return RepositorySchema[]|array
     */
    public function getRepositories(): array
    {
        return $this->repositories;
    }

    /**
     * @param RepositorySchema[]|array $repositories
     */
    public function setRepositories(array $repositories): void
    {
        $this->repositories = $repositories;
    }

    /**
     * @return array
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * @param array $config
     */
    public function setConfig(array $config): void
    {
        $this->config = $config;
    }

    /**
     * @return ScriptSchema[]|array
     */
    public function getScripts(): array
    {
        return $this->scripts;
    }

    /**
     * @param ScriptSchema[]|array $scripts
     */
    public function setScripts(array $scripts): void
    {
        $this->scripts = $scripts;
    }

    /**
     * @return array
     */
    public function getExtra(): array
    {
        return $this->extra;
    }

    /**
     * @param array $extra
     */
    public function setExtra(array $extra): void
    {
        $this->extra = $extra;
    }

    /**
     * @return array|string[]
     */
    public function getBin(): array
    {
        return $this->bin;
    }

    /**
     * @param array|string[] $bin
     */
    public function setBin(array $bin): void
    {
        $this->bin = $bin;
    }

    /**
     * @return string|null
     */
    public function getArchive(): ?string
    {
        return $this->archive;
    }

    /**
     * @param string|null $archive
     */
    public function setArchive(?string $archive): void
    {
        $this->archive = $archive;
    }

    /**
     * @return bool|string|null
     */
    public function getAbandoned()
    {
        return $this->abandoned;
    }

    /**
     * @param bool|string|null $abandoned
     */
    public function setAbandoned($abandoned): void
    {
        $this->abandoned = $abandoned;
    }

    /**
     * @return array
     */
    public function getNonFeatureBranches(): array
    {
        return $this->nonFeatureBranches;
    }

    /**
     * @param array $nonFeatureBranches
     */
    public function setNonFeatureBranches(array $nonFeatureBranches): void
    {
        $this->nonFeatureBranches = $nonFeatureBranches;
    }

    public function toArray()
    {
        return collect([
            'name' => $this->name,
            'description' => $this->description,
            'version' => $this->version,
            'type' => $this->type,
            'keywords' => $this->keywords,
            'homepage' => $this->homepage,
            'readme' => $this->readme,
            'time' => $this->time ? $this->time->format('Y-m-d H:i:s') : null,
            'license' => $this->license,
            'authors' => collect($this->authors),
            'support' => $this->support->toArray(),
            'funding' => collect($this->funding),
            'require' => collect($this->require)->mapWithKeys(fn(PackageSchema $package) => [$package->getName() => $package->getVersion()]),
            'require-dev' => collect($this->requireDev)->mapWithKeys(fn(PackageSchema $package) => [$package->getName() => $package->getVersion()]),
            'conflict' => collect($this->conflict)->mapWithKeys(fn(PackageSchema $package) => [$package->getName() => $package->getVersion()]),
            'replace' => collect($this->replace)->mapWithKeys(fn(PackageSchema $package) => [$package->getName() => $package->getVersion()]),
            'provide' => collect($this->provide)->mapWithKeys(fn(PackageSchema $package) => [$package->getName() => $package->getVersion()]),
            'suggest' => collect($this->suggest)->mapWithKeys(fn(PackageSchema $package) => [$package->getName() => $package->getVersion()]),
            'autoload' => $this->autoload,
            'autoload-dev' => $this->autoloadDev,
            'include-path' => $this->includePath,
            'target-dir' => $this->targetDir,
            'minimum-stability' => $this->minimumStability,
            'prefer-stable' => $this->preferStable,
            'repositories' => collect($this->repositories),
            'config' => $this->config,
            'scripts' => collect($this->scripts),
            'extra' => $this->extra,
            'bin' => $this->bin,
            'archive' => $this->archive,
            'abandoned' => $this->abandoned,
            'non-feature-branches' => $this->nonFeatureBranches
        ])->filter(fn($val) => $val !== [] && $val !== null && ($val instanceof Collection ? $val->count() > 0 : true))->toArray();
    }

}
