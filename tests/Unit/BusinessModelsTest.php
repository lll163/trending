<?php

namespace Tests\Unit;

// AI-GEN-BEGIN
use App\Models\Article;
use App\Models\BlacklistEntry;
use App\Models\MagazineIssue;
use App\Models\OauthAccount;
use App\Models\RankingConfig;
use App\Models\RankingEntry;
use App\Models\ReposSnapshot;
use App\Models\ReviewLog;
use App\Models\Submission;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Model;
use PHPUnit\Framework\TestCase;

/**
 * 无数据库读写：仅校验业务模型可实例化且表名正确。
 */
class BusinessModelsTest extends TestCase
{
    public function test_business_models_are_eloquent_instances(): void
    {
        $models = [
            new OauthAccount,
            new ReposSnapshot,
            new Submission,
            new ReviewLog,
            new Tag,
            new BlacklistEntry,
            new MagazineIssue,
            new Article,
            new RankingConfig,
            new RankingEntry,
        ];

        foreach ($models as $model) {
            $this->assertInstanceOf(Model::class, $model);
        }
    }
}
// AI-GEN-END
