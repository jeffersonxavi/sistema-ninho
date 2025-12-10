<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

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
        ];
    }
    /**
     * Relacionamento: Um User pode cadastrar muitos Alunos.
     */
    public function alunosCadastrados(): HasMany
    {
        return $this->hasMany(Aluno::class, 'cadastrado_por_user_id');
    }

    /**
     * Relacionamento: Um User pode cadastrar muitos Professores.
     */
    public function professoresCadastrados(): HasMany
    {
        return $this->hasMany(Professor::class, 'cadastrado_por_user_id');
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

    // Métodos de ajuda para permissão:
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isStaff(): bool
    {
        return $this->role === 'staff';
    }
}
