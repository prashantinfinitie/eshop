<section class="main-content mb-15 deeplink_wrapper">
    <div class="row">
        <div class="col-md-12 col-12 mt-4 pt-2">
            <div class="tab-content" id="pills-tabContent">
                <div class="tab-pane fade bg-white show active shadow rounded p-4 text-center" id="dash" role="tabpanel" aria-labelledby="dashboard">
                    <i class="uil uil-check-circle fs-100 text-success"></i>
                    <h4 class="h4 text-success"><?= !empty($this->lang->line('wallet_recharged')) ? str_replace('\\', '', $this->lang->line('wallet_recharged')) : 'Wallet Recharged' ?></h4>
                    <p><?= !empty($this->lang->line('wallet_recharged_successfully')) ? str_replace('\\', '', $this->lang->line('wallet_recharged_successfully')) : 'Wallet recharged succesfully' ?></p>
                    <a class="btn btn-primary btn-sm" href="<?=base_url('my_account/wallet')?>"><?= !empty($this->lang->line('return_to_wallet')) ? str_replace('\\', '', $this->lang->line('return_to_wallet')) : 'Return to wallet' ?></a>
                </div>
            </div>
        </div>
    </div>
</section>