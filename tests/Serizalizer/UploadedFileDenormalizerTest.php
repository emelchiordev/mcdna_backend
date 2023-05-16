<?

use App\Serializer\UploadedFileDenormalizer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploadedFileDenormalizerTest extends TestCase
{
    public function testDenormalize()
    {
        $denormalizer = new UploadedFileDenormalizer();

        $data = 'src/images/products';
        $type = UploadedFile::class;
        $format = 'json';
        $context = [];

        $result = $denormalizer->denormalize($data, $type, $format, $context);

        $this->assertInstanceOf(UploadedFile::class, $result);
    }

    public function testSupportsDenormalization()
    {
        $denormalizer = new UploadedFileDenormalizer();

        $data = new UploadedFile('src/images/products', 'filename');
        $type = UploadedFile::class;
        $format = 'json';

        $result = $denormalizer->supportsDenormalization($data, $type, $format);

        $this->assertTrue($result);
    }
}
