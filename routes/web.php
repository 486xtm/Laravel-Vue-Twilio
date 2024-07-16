<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EstadoController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReadyMessageController;
use App\Http\Controllers\FunnelStepController;
use App\Http\Controllers\FunnelController;
use App\Http\Controllers\CidadeController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserQueueController;
use App\Http\Controllers\OrigemController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\LeadMessageController;
use App\Http\Controllers\LeadScheduleController;
use App\Http\Controllers\LeadNotesController;
use App\Http\Controllers\ChannelController;
use App\Http\Controllers\ProposalController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\FormCampaignController;
use App\Http\Controllers\ReceivableController;
use App\Http\Controllers\BotController;
use App\Http\Controllers\ArchievementController;
use App\Http\Controllers\BotWorkFlowController;
use App\Http\Controllers\SocialAuthFacebookController;
use App\Http\Controllers\MessengerController;
use App\Http\Controllers\SmsController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\DeletePhoneDataController;
use App\Mail\NotificationMail;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    /*return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);*/

    return redirect('login');
});

Route::get('/play', function () {
    return Inertia::render('Comp');
});

Route::get('/chat', function () {
    return Inertia::render('Whatsapp');
});

Route::controller(SocialAuthFacebookController::class)->group(function () {
    Route::get('/redirect', 'redirect')->name('redirect');
    Route::get('/callback', 'callback')->name('callback');
});

Route::get('/configuracao', function () {
    return Inertia::render('Settings');
})->middleware(['auth', 'verified'])->name('settings');

Route::controller(ReportController::class)->group(function () {
    Route::get('/relatorios', 'index')->name('reports');    
});
Route::post('/receive-message', [SmsController::class, 'receiveMessage']);
Route::group(['middleware' => ['auth', 'verified']], function(){

    Route::controller(DashboardController::class)->group(function () {
        Route::get('/dashboard', 'index')->name('dashboard');
    });

    Route::controller(PaymentController::class)->group(function () {
        Route::get('/relatorio-de-pagamentos', 'index')->name('payments.index');
        Route::get('/relatorio-de-pagamentos/conciliacao', 'receivables')->name('payment.conciliation');
        Route::get('/relatorio-de-pagamentos/associacao', 'association')->name('payment.association');
        Route::post('/relatorio-de-pagamentos/upload', 'importFile')->name('payment.upload');   
        Route::put('/relatorio-de-pagamentos/{payment}/desassociacao', 'disassociation')->name('payment.disassociation');  
        Route::delete('relatorio-de-pagamentos/{payment}','destroy')->name('payment.destroy');     
    });
    
    Route::controller(FunnelController::class)->group(function () {
        Route::get('/funil', 'index')->name('funnel.index');
        Route::put('/funil/{id}', 'update')->name('funnel.update');
        Route::put('/funil/{lists}/move', 'move')->name('funnel.move');
    });
    
    Route::controller(LeadController::class)->group(function () {
        Route::get('/lead', 'index')->name('lead.index');
        Route::get('/lead/criar', 'create')->name('lead.create');
        Route::get('/lead/{lead}/', 'view')->name('lead.view');
        Route::get('/lead/{lead}/editar', 'edit')->name('lead.edit');
        Route::get('/leads','getLeads'); 
        Route::get('/leadswaiting','getWaiting'); 
        Route::get('/leadsschedule','getSchedule'); 
        Route::get('/leadsfavorite','getFavorite'); 
        Route::get('/leadspendding','getPendding'); 
        Route::get('/leads-contador','getNewLeads')->name('new-leads'); 
        Route::get('/lead-relatorio','export')->name('lead.export'); 
        Route::put('/lead-funelstep', 'setFunnelStep')->name('lead.funnelstep');
        Route::put('/lead/{lead}/favoritar', 'setFavorite')->name('lead.favorite');    
        Route::put('/lead/{lead}/classificar', 'setStars')->name('lead.star');    
        Route::put('/lead/{lead}/encaminhar', 'forward')->name('lead.forward');    
        Route::put('/lead/{lead}', 'update')->name('lead.update');
        Route::put('/lead-arquivar', 'setArchive')->name('lead.archive');
        Route::put('/leads-encaminhados', 'leadsForward')->name('lead.multi-forward');
        Route::delete('/lead/{lead}', 'destroy')->name('lead.destroy');        
        Route::post('/lead', 'store')->name('lead.store');
        
    });

    Route::controller(LeadMessageController::class)->group(function () {
        Route::get('/lead-message', 'index')->name('leadmessage.index');
        Route::get('/lead-message/criar', 'create')->name('leadmessage.create');
        Route::get('/lead-message/{lead}/', 'view')->name('leadmessage.view');
        Route::get('/lead-message/{lead}/editar', 'edit')->name('leadmessage.edit');
        Route::put('/lead-message/{lead}', 'update')->name('leadmessage.update');
        Route::delete('/lead-message/{lead}', 'destroy')->name('leadmessage.destroy');
        Route::post('/lead-message', 'store')->name('leadmessage.store');
    });

    Route::controller(ProposalController::class)->group(function () {
        Route::get('/proposta','index')->name('proposal.index');
        Route::get('/proposta/criar','create')->name('proposal.create');
        Route::get('/proposta/{proposal}/editar', 'edit')->name('proposal.edit');
        Route::put('/proposta/{proposal}','update')->name('proposal.update');
        Route::put('/proposta/{proposal}/aprovar', 'approve')->name('proposal.approve');  
        Route::put('/proposta/{proposal}/apropriacao', 'appropriation')->name('proposal.appropriation');  
        Route::put('/proposta/{proposal}/desprovacao', 'unpprove')->name('proposal.unpprove');  
        Route::delete('/proposta/{proposal}','destroy')->name('proposal.destroy');
        Route::post('/proposta', 'store')->name('proposal.store');
    });

    Route::controller(ReceivableController::class)->group(function () {
        Route::get('/conta-receber','index')->name('receivable.index');
        Route::get('/conta-receber/{receivable}/editar', 'edit')->name('receivable.edit');
        Route::get('/conta-receber-relatorio','export')->name('receivable.export'); 
        Route::put('/conta-receber/{receivable}','update')->name('receivable.update');
        Route::put('/conta-receber/{receivable}/aprovar', 'approve')->name('receivable.approve');          
        Route::put('/conta-receber/{receivable}/desprovacao', 'unpprove')->name('receivable.unpprove');  
        Route::delete('/conta-receber/{receivable}','destroy')->name('receivable.destroy');        
        Route::post('/conta-receber', 'store')->name('receivable.store');
    });

    Route::controller(ArchievementController::class)->group(function () {
        Route::get('/arquivamento','index')->name('archievement.index');
        Route::get('/arquivamento-relatorio','export')->name('archievement.export');         
    });

    Route::controller(MessengerController::class)->group(function () {
        Route::get('/chat','index')->name('messenger.index');        
    });

   
    Route::post('/vue-message', [SmsController::class, 'VueMessage']);
    Route::post('/media-message', [SmsController::class, 'MediaMessage']);
    Route::post('/get-message', [ChatController::class, 'getData']);
    Route::get('/get-message', [ChatController::class, 'getPhoneNumbers']);
    Route::get('/download-media', [ChatController::class, 'getDownload']);
    Route::post('/delete-phone', [DeletePhoneDataController::class, 'deletePhone']);
    Route::post('/delete-message', [DeletePhoneDataController::class, 'deleteMessage']);

    Route::controller(UserQueueController::class)->group(function () {
        Route::get('/fila', 'index')->name('userqueue.index');
        Route::get('/fila/status', 'status')->name('userqueue.status');
    });

    Route::controller(CampaignController::class)->group(function () {
        Route::get('/campanha','index')->name('campaign.index');
        Route::get('/campanha/criar','create')->name('campaign.create');
        Route::get('/campanha/{campaign}/editar', 'edit')->name('campaign.edit');
        Route::put('/campanha/{campaign}','update')->name('campaign.update');
        Route::delete('/campanha/{campaign}','destroy')->name('campaign.destroy');
        Route::post('/campanha', 'store')->name('campaign.store');
    });

    Route::controller(FormCampaignController::class)->group(function () {
        Route::get('/formulario','index')->name('formcampaign.index');
        Route::get('/formulario/criar','create')->name('formcampaign.create');
        Route::get('/formulario/{form}/editar', 'edit')->name('formcampaign.edit');
        Route::put('/formulario/{form}','update')->name('formcampaign.update');
        Route::delete('/formulario/{form}','destroy')->name('formcampaign.destroy');
        Route::post('/formulario', 'store')->name('formcampaign.store');
    });

});

Route::controller(LeadScheduleController::class)->group(function () {
    Route::get('/lead-schedule', 'index')->name('leadschedule.index');
    Route::get('/lead-schedule/criar', 'create')->name('leadschedule.create');
    Route::get('/lead-schedule/{lead}/', 'view')->name('leadschedule.view');
    Route::get('/lead-schedule/{lead}/editar', 'edit')->name('leadschedule.edit');
    Route::put('/lead-schedule/{lead}', 'update')->name('leadschedule.update');
    Route::delete('/lead-schedule/{lead}', 'destroy')->name('leadschedule.destroy');
    Route::post('/lead-schedule', 'store')->name('leadschedule.store');
})->middleware(['auth', 'verified']);

Route::controller(LeadNotesController::class)->group(function () {
    Route::post('/lead-note', 'store')->name('leadsnote.store');
})->middleware(['auth', 'verified']);

Route::controller(CidadeController::class)->group(function () {
    Route::get('/cidade', 'index')->name('cidade.index');
    Route::get('/cidade/criar', 'create')->name('cidade.create');
    Route::get('/cidade/{cidade}/editar', 'edit')->name('cidade.edit');
    Route::put('/cidade/{cidade}', 'update')->name('cidade.update');
    Route::delete('/cidade/{cidade}', 'destroy')->name('cidade.destroy');
    Route::post('/cidade', 'store')->name('cidade.store');
})->middleware(['auth', 'verified']);

Route::controller(EstadoController::class)->group(function () {
    Route::get('/estado','index')->name('estado.index');
    Route::get('/estado/criar','create')->name('estado.create');
    Route::get('/estado/{estado}/editar', 'edit')->name('estado.edit');
    Route::put('/estado/{estado}','update')->name('estado.update');
    Route::delete('/estado/{estado}','destroy')->name('estado.destroy');
    Route::post('/estado', 'store')->name('estado.store');
})->middleware(['auth', 'verified']);

Route::controller(CompanyController::class)->group(function () {
    Route::get('/empresa','index')->name('company.index');
    Route::get('/empresa/criar','create')->name('company.create');
    Route::get('/empresa/{company}/editar', 'edit')->name('company.edit');
    Route::get('/empresa/listar','getCompanies')->name('getcompanies');
    Route::put('/empresa/{company}','update')->name('company.update');
    Route::delete('/empresa/{company}','destroy')->name('company.destroy');
    Route::post('/empresa', 'store')->name('company.store');
})->middleware(['auth', 'verified']);

Route::controller(ChannelController::class)->group(function () {
    Route::get('/canal','index')->name('channel.index');
    Route::get('/canal/criar','create')->name('channel.create');
    Route::get('/canal/{channel}/editar', 'edit')->name('channel.edit');
    Route::put('/canal/{channel}','update')->name('channel.update');
    Route::delete('/canal/{channel}','destroy')->name('channel.destroy');
    Route::post('/canal', 'store')->name('channel.store');
})->middleware(['auth', 'verified']);

Route::controller(OrigemController::class)->group(function () {
    Route::get('/origem','index')->name('origem.index');
    Route::get('/origem/criar','create')->name('origem.create');
    Route::get('/origem/{origem}/editar', 'edit')->name('origem.edit');
    Route::put('/origem/{origem}','update')->name('origem.update');
    Route::delete('/origem/{channel}','destroy')->name('origem.destroy');
    Route::post('/origem', 'store')->name('origem.store');
})->middleware(['auth', 'verified']);

Route::controller(NotificationController::class)->group(function () {
    Route::get('/notificacao','index')->name('notification.index');
    Route::get('/notificacao/criar','create')->name('notification.create');
    Route::get('/notificacao/{notification}/editar', 'edit')->name('notification.edit');
    Route::get('/notificacao/show', 'show')->name('notification.show');
    Route::put('/notificacao/{notification}','update')->name('notification.update');
    Route::delete('/notificacao/{notification}','destroy')->name('notification.destroy');
    Route::post('/notificacao', 'store')->name('notification.store');
})->middleware(['auth', 'verified']);

Route::controller(StatusController::class)->group(function () {
    Route::get('/status','index')->name('status.index');
    Route::get('/status/criar','create')->name('status.create');
    Route::get('/status/{status}/editar', 'edit')->name('status.edit');
    Route::put('/status/{status}','update')->name('status.update');
    Route::delete('/status/{status}','destroy')->name('status.destroy');
    Route::post('/status', 'store')->name('status.store');
})->middleware(['auth', 'verified']);

Route::controller(ProductController::class)->group(function () {
    Route::get('/produto','index')->name('product.index');
    Route::get('/produto/criar','create')->name('product.create');
    Route::get('/produto/{product}/editar', 'edit')->name('product.edit');
    Route::put('/produto/{product}','update')->name('product.update');
    Route::delete('/produto/{status}','destroy')->name('product.destroy');
    Route::post('/produto', 'store')->name('product.store');
})->middleware(['auth', 'verified']);

Route::controller(FunnelStepController::class)->group(function () {
    Route::get('/etapa-de-funil','index')->name('funnelstep.index');
    Route::get('/etapa-de-funil/criar','create')->name('funnelstep.create');
    Route::get('/etapa-de-funil/{step}/editar', 'edit')->name('funnelstep.edit');
    Route::put('/etapa-de-funil/{step}','update')->name('funnelstep.update');
    Route::delete('/etapa-de-funil/{status}','destroy')->name('funnelstep.destroy');
    Route::post('/etapa-de-funil', 'store')->name('funnelstep.store');
})->middleware(['auth', 'verified']);

Route::controller(ReadyMessageController::class)->group(function () {
    Route::get('/messagem-rapida','index')->name('message.index');
    Route::get('/messagem-rapida/criar','create')->name('message.create');
    Route::get('/messagem-rapida/{message}/editar', 'edit')->name('message.edit');
    Route::put('/messagem-rapida/{message}','update')->name('message.update');
    Route::delete('/messagem-rapida/{message}','destroy')->name('message.destroy');
    Route::post('/messagem-rapida', 'store')->name('message.store');
})->middleware(['auth', 'verified']);

Route::controller(UserController::class)->group(function () {
    Route::get('/usuario','index')->name('user.index');    
    Route::get('/usuario/criar','create')->name('user.create');
    Route::get('/usuario/setcompany', 'setCompany')->name('user.setcompany');
    Route::get('/usuario/getcompany', 'getCompany')->name('user.getcompany');
    Route::get('/usuario/{user}/editar', 'edit')->name('user.edit');
    Route::get('/usuario/listar','getUsers')->name('getusers');
    Route::put('/usuario/{user}','update')->name('user.update');
    Route::put('/usuario/{user}/pausar','pause')->name('user.pause');    
    Route::put('/usuario/{user}/play','play')->name('user.play');    
    Route::put('/usuario/{user}/destroy','destroy')->name('user.destroy');    
    Route::post('/usuario', 'store')->name('user.store');
})->middleware(['auth', 'verified']);

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::controller(BotController::class)->group(function () {
    Route::get('/bot','index')->name('bot.index');
    Route::get('/bot/{bot}/editar', 'edit')->name('bot.edit');
    Route::put('/bot/{bot}','update')->name('bot.update');
    Route::put('/bot/{bot}/status', 'setStatus')->name('bot.status');
    Route::post('/bot', 'handle');
})->middleware(['auth', 'verified']);


Route::controller(BotWorkFlowController::class)->group(function () {
    Route::put('/botworkflow/{bot}','update')->name('botworkflow.update');    
})->middleware(['auth', 'verified']);


Route::get('/email', function(){
    return new NotificationMail();
});

Route::get('password-reset', function() {
    $user = App\Models\User::where('email', 'luiz78774@gmail.com')->first();
    $user->password = Hash::make('12345678');
    $user->save();
 
    return 'Success!';
});

Route::get('clear-session', function() {
    
    Session::flush();

    return 'Success!';
});


require __DIR__.'/auth.php';

