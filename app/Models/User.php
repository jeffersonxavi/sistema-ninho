<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'cpf',
        'telefone',
        'data_nascimento',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'data_nascimento' => 'date',
        ];
    }
    /**
     * Relacionamento: Um User pode cadastrar muitos Alunos.
     */
    public function alunosCadastrados(): HasMany
    {
        return $this->hasMany(Aluno::class, 'cadastrado_por_user_id');
    }
    public function salasResponsavel()
    {
        return $this->hasMany(Sala::class, 'responsavel_id');
    }

    public function getSalasResponsavelIds()
    {
        return $this->salasResponsavel()->pluck('id')->toArray();
    }

    // VERIFIQUE ESTE RELACIONAMENTO (CRUCIAL!)
    public function professor(): HasOne
    {
        // Certifique-se que o namespace App\Models\Professor existe
        return $this->hasOne(\App\Models\Professor::class, 'user_id');
    }
    /**
     * Relacionamento: Um User pode cadastrar muitas Turmas.
     */
    public function turmasCadastradas(): HasMany
    {
        return $this->hasMany(Turma::class, 'cadastrado_por_user_id');
    }

    /**
     * Relacionamento: Um User pode cadastrar muitas Salas.
     */
    public function salasCadastradas(): HasMany
    {
        return $this->hasMany(Sala::class, 'cadastrado_por_user_id');
    }

    /**
     * Relacionamento: Um User pode registrar a baixa de muitos Pagamentos.
     */
    public function pagamentosRegistrados(): HasMany
    {
        return $this->hasMany(Pagamento::class, 'registrado_por_user_id');
    }

    // app/Models/User.php

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isStaff(): bool
    {
        return $this->role === 'staff';

        // ou: return $this->role === 'admin' || $this->role === 'staff';
    }
    public function getGerenciamentoTurmasIds(): array
    {
        // Se for Admin, retorna um array vazio. O Controller saberá que deve ignorar o filtro.
        if ($this->isAdmin()) {
            return [];
        }

        // Carrega o relacionamento Professor
        $professor = $this->professor;

        if (!$professor) {
            // Se o Staff não está vinculado a um registro Professor, ele não gerencia turmas.
            return [0]; // Retorna [0] para que o whereIn não retorne nada (ID 0 é improvável)
        }

        // Pega todos os IDs de Turmas vinculados a este Professor
        // Assumindo um relacionamento N:M entre Professor e Turma (pivot turma_professor)
        $turmasIds = DB::table('turma_professor')
            ->where('professor_id', $professor->id)
            ->pluck('turma_id')
            ->toArray();

        return $turmasIds;
    }
}
