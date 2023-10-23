<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\auth\AuthControllerApi;
use App\Http\Controllers\ProjectsController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\OperationController;
use App\Http\Controllers\PackagesController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\PaymentActionsController;
use App\Http\Controllers\CountriesController;
use App\Http\Controllers\CitiesController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\RemoteConnectionController;
use App\Http\Controllers\Api\UsersControllerApi;
use App\Http\Controllers\classes\general\GeneralServiceController;
use App\Http\Controllers\SellerInfoController;
use App\Http\Controllers\FollowersController;
use App\Http\Controllers\ProductsControllerResource;
use App\Http\Controllers\CategoriesControllerResource;
use App\Http\Controllers\ArticlesControllerResource;
use App\Http\Controllers\UsersAddressControllerResource;
use App\Http\Controllers\AreasControllerResource;
use App\Http\Controllers\CitiesControllerResource;
use App\Http\Controllers\FinancialReconciliationsControllerResource;
use App\Http\Controllers\CountriesControllerResource;
use App\Http\Controllers\GovermentsControllerResource;
use App\Http\Controllers\SearchesController;
use App\Http\Controllers\FavouriteController;
use App\Http\Controllers\RateController;
use App\Http\Controllers\TaxesController;
use App\Http\Controllers\CareControllerResource;
use App\Http\Controllers\ProductsCaresAlerts;
use App\Http\Controllers\UsersProductsCares;
use App\Http\Controllers\BestController;
use App\Http\Controllers\VisaBankControllerResource;
use App\Http\Controllers\CustomerOrdersControllerResource;
use App\Http\Controllers\AllSellersDataController;
use App\Http\Controllers\AIController;


Route::group(['middleware'=>'changeLang'],function (){
    Route::get('/test',[AuthControllerApi::class,'test']);
    Route::get('/tax',[TaxesController::class,'index']);


    Route::group(['prefix'=>'/auth'],function(){
        Route::post('/register-check',[AuthControllerApi::class,'register_post']);
        Route::post('/login',[AuthControllerApi::class,'login_api']);
        Route::post('/check-otp',[AuthControllerApi::class,'check_otp']);
        Route::post('/logout',[AuthControllerApi::class,'logout_api']);

    });


    Route::group(['prefix'=>'/seller','middleware'=>['CheckApiAuth','checkSeller']],function(){
        Route::post('/save-store',[SellerInfoController::class,'save_store']);
        Route::post('/save-bank',[SellerInfoController::class,'save_bank']);
        Route::post('/save-commercial-infos',[SellerInfoController::class,'save_commercial_infos']);
    });

    Route::post('/validate-user',[AuthControllerApi::class,'validate_user']);
    Route::get('/user',[AuthControllerApi::class,'user'])->middleware('CheckApiAuth');

    // ---------------------start of followers actions --------------------
    Route::group(['prefix'=>'/addresses','middleware'=>'CheckApiAuth'],function (){
        Route::post('/set-to-default',[UsersAddressControllerResource::class,'set_to_default']);
    });
    // ---------------------end of followers actions --------------------

    // ---------------------start of categories actions --------------------
    Route::group(['prefix'=>'/categories-data','middleware'=>'CheckApiAuth'],function (){
        Route::get('/cat-questions-features',[CategoriesControllerResource::class,'cat_questions_features']);

    });
    // ---------------------end of categories actions --------------------


    // ---------------------start of followers actions --------------------
    Route::group(['prefix'=>'/follow','middleware'=>'CheckApiAuth'],function (){
        Route::post('/toggle',[FollowersController::class,'toggle']);
        Route::get('/all',[FollowersController::class,'all']);

    });
    // ---------------------end of followers actions --------------------






    // ---------------------start of products actions --------------------
    Route::group(['prefix'=>'/products','middleware'=>'CheckApiAuth'],function (){
        Route::post('/toggle-fav',[ProductsControllerResource::class,'toggle_fav']);
        Route::post('/toggle-like',[ProductsControllerResource::class,'toggle_like']);
    });
    // ---------------------end of products actions --------------------

    // ---------------------start of products actions --------------------
    Route::group(['prefix'=>'/best','middleware'=>'CheckApiAuth'],function (){
        Route::get('/products-rates',[BestController::class,'rates']);
        Route::get('/products-orders',[BestController::class,'orders']);
    });
    // ---------------------end of products actions --------------------

    // ---------------------start of products cares actions --------------------
    Route::group(['prefix'=>'/products-care','middleware'=>'CheckApiAuth'],function (){
        Route::get('/',[UsersProductsCares::class,'get_products_cares']);
        //Route::get('/{id}',[UsersProductsCares::class,'find']);
        Route::get('/questions-of-product-care',[UsersProductsCares::class,'questions']);
        Route::post('/add-to-care',[UsersProductsCares::class,'add']);
        Route::post('/custom-care',[UsersProductsCares::class,'make_custom_product_care']);

    });


    // ---------------------start of favourite actions --------------------
    Route::group(['prefix'=>'/favourite','middleware'=>'CheckApiAuth'],function (){
        Route::get('/',[FavouriteController::class,'index']);

    });
    // ---------------------end of favourite actions --------------------

    // ---------------------start of searches actions --------------------

    Route::group(['prefix'=>'/searches','middleware'=>'CheckApiAuth'],function (){
        Route::get('/products',[SearchesController::class,'products']);

    });
    // ---------------------end of searches actions --------------------

    // ---------------------start of orders actions --------------------
    Route::group(['prefix'=>'/orders','middleware'=>'CheckApiAuth'],function (){
        Route::get('/',[OrdersController::class,'all_orders']);
        Route::post('/make',[OrdersController::class,'make_order']);
        Route::post('/update-status',[OrdersController::class,'update_status']);

    });
    // ---------------------end of orders actions --------------------

    // ---------------------start of custom orders actions --------------------
    Route::group(['prefix'=>'/sellers','middleware'=>'CheckApiAuth'],function (){
        Route::get('/',[AllSellersDataController::class,'index']);
        Route::get('/replies',[AllSellersDataController::class,'replies_custom_orders']);
        Route::post('/reply-custom-order',[CustomerOrdersControllerResource::class,'seller_reply']);
        Route::post('/send-request',[CustomerOrdersControllerResource::class,'send_request']);
    });
    // ---------------------end of custom orders actions --------------------

    // ---------------------start of custom orders actions --------------------
    Route::group(['prefix'=>'/clients','middleware'=>'CheckApiAuth'],function (){
        Route::post('/reply-custom-order',[CustomerOrdersControllerResource::class,'client_reply']);
        Route::post('/ai-images',[AIController::class,'index'])->withoutMiddleware('CheckApiAuth');
        Route::get('/ai-questions',[AIController::class,'ai_questions'])->withoutMiddleware('CheckApiAuth');
    });
    // ---------------------end of custom orders actions --------------------



    // ---------------------start of rates actions --------------------
    Route::group(['prefix'=>'/rates','middleware'=>'CheckApiAuth'],function (){
        Route::post('/make',[RateController::class,'make']);

    });
    // ---------------------end of rates actions --------------------


    // ---------------------start of articles actions --------------------
    Route::group(['prefix'=>'/articles','middleware'=>'CheckApiAuth'],function (){
        Route::post('/save-comment',[ArticlesControllerResource::class,'save_comment']);
        Route::post('/save-like',[ArticlesControllerResource::class,'save_like']);
        Route::post('/toggle-fav',[ArticlesControllerResource::class,'toggle_fav']);

    });
    // ---------------------end of articles actions --------------------



    // ---------------------start of users actions --------------------
    Route::group(['prefix'=>'/profile','middleware'=>'CheckApiAuth'],function (){
        Route::post('/update-personal-info',[UsersController::class,'update_personal_info']);
        Route::post('/visit-seller',[UsersController::class,'visit_seller']);
        Route::post('/report',[UsersController::class,'quick_report']);
    });
    // ---------------------end of users actions --------------------





    Route::post('/notifications',[NotificationsController::class,'index'])->middleware('CheckApiAuth');
    Route::get('/notifications/statistics',[NotificationsController::class,'statistics'])->middleware('CheckApiAuth');

    //----------------------- start of orders------------------

    //----------------------- end of orders------------------

    //----------------------- start of dashboard------------------
    Route::group(['prefix'=>'/dashboard','middleware'=>['CheckApiAuth']],function(){
        Route::post('/users',[DashboardController::class,'get_users']);
       Route::group(['prefix'=>'/packages'],function(){
          Route::post('/save',[DashboardController::class,'save_package']);
       });
    });
    Route::group(['prefix'=>'/tickets','middleware'=>['CheckApiAuth']],function(){
        Route::post('/save-cat',[DashboardController::class,'save_tickets_cats']);
        Route::get('/cats',[DashboardController::class,'get_tickets_cats']);
    });
    //----------------------- end of dashboard------------------


    //=========================start of tickets==================
    Route::group(['prefix'=>'/tickets','middleware'=>['CheckApiAuth']],function(){
        Route::post('/save-cat',[DashboardController::class,'save_tickets_cats']);
        Route::get('/all',[DashboardController::class,'all_tickets']);
        Route::post('/make',[DashboardController::class,'make_ticket']);
        Route::post('/reply',[DashboardController::class,'reply_ticket']);
        Route::post('/messages',[DashboardController::class,'messages']);
    });
    //=========================end of tickets==================

    // delete item
    Route::post('/delete-item',[GeneralServiceController::class,'delete_item']);



    Route::resources([
        'products'=>ProductsControllerResource::class,
        'categories'=>CategoriesControllerResource::class,
        'articles'=>ArticlesControllerResource::class,
        'addresses'=>UsersAddressControllerResource::class,
        'countries'=>CountriesControllerResource::class,
        'governments'=>GovermentsControllerResource::class,
        'cities'=>CitiesControllerResource::class,
        'areas'=>AreasControllerResource::class,
        'financial'=>FinancialReconciliationsControllerResource::class,
        'care'=>CareControllerResource::class,
        'online-payment'=>VisaBankControllerResource::class,
        'custom-orders'=>CustomerOrdersControllerResource::class
    ]);





});
