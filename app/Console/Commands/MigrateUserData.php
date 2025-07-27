use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use PDO;

class MigrateUserData extends Command
{
    protected $signature = 'user:migrate-data {--dry-run}';
    protected $description = 'Migrate user data from SQLite to MySQL';

    public function handle()
    {
        $sqlitePath = base_path('database/old_database.sqlite');

        if (!file_exists($sqlitePath)) {
            $this->error('SQLite database not found.');
            return 1;
        }

        $sqlite = new PDO("sqlite:$sqlitePath");
        $mysql = DB::connection('mysql');

        $rows = $sqlite->query('SELECT name, email, password FROM users')->fetchAll(PDO::FETCH_ASSOC);

        foreach ($rows as $row) {
            $email = $row['email'];
            $existing = $mysql->table('users')->where('email', $email)->first();

            if ($existing) {
                $this->line("Skipping existing user: {$email}");
                continue;
            }

            // Check if password is hashed
            $isHashed = password_get_info($row['password'])['algo'] !== 0;
            $password = $isHashed ? $row['password'] : Hash::make($row['password']);

            if (!$this->option('dry-run')) {
                $mysql->table('users')->insert([
                    'name' => $row['name'],
                    'email' => $email,
                    'email_verified_at' => now(),
                    'password' => $password,
                    'role' => 'default',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $this->line(($this->option('dry-run') ? '[Dry Run] ' : '') . "Migrated user: {$email}");
        }

        $this->info('User migration complete!');
        return 0;
    }
}
