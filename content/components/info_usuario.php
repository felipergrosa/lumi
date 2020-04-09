<?php
@session_start();

?>
    <div class="header-btn-lg pr-0">
        <div class="widget-content p-0">
            <div class="widget-content-wrapper">
                <div class="widget-content-left">
                    <div class="btn-group">
                        <a data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="p-0 btn">
                            <img width="42" class="rounded-circle" src="assets/images/avatars/<?php echo $_SESSION['nobre_usuario_foto']; ?>" alt="">
                            <i class="fa fa-angle-down ml-2 opacity-8"></i>
                        </a>
                        <div tabindex="-1" role="menu" aria-hidden="true" class="rm-pointers dropdown-menu-lg dropdown-menu dropdown-menu-right">
                            <div class="dropdown-menu-header">
                                <div class="dropdown-menu-header-inner bg-info">
                                    <div class="menu-header-image opacity-2" style="background-image: url('assets/images/dropdown-header/city3.jpg');"></div>
                                    <div class="menu-header-content text-left">
                                        <div class="widget-content p-0">
                                            <div class="widget-content-wrapper">
                                                <div class="widget-content-left mr-3">
                                                    <img width="42" class="rounded-circle"
                                                         src="assets/images/avatars/<?php echo $_SESSION['nobre_usuario_foto']; ?>"
                                                         alt="">
                                                </div>
                                                <div class="widget-content-left">
                                                    <div class="widget-heading"><?php echo $_SESSION['nobre_usuario_nome']; ?>
                                                    </div>
                                                    <div class="widget-subheading opacity-8"><?php echo $_SESSION['nobre_usuario_descricao']; ?>
                                                    </div>
                                                </div>
                                                <div class="widget-content-right mr-2">
                                                    <button onclick="location.href='login.html'" class="btn-pill btn-shadow btn-shine btn btn-focus">Sair
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="scroll-area-xs" style="height: 150px;">
                                <div class="scrollbar-container ps">
                                    <ul class="nav flex-column">
                                        <li class="nav-item-header nav-item">Atividade
                                        </li>


                                        <li class="nav-item">
                                            <a href="javascript:void(0);" class="nav-link">Mensagens
                                                <div class="ml-auto badge badge-warning">512
                                                </div>
                                            </a>
                                        </li>

                                    </ul>
                                </div>
                            </div>

                            <ul class="nav flex-column">
                                <li class="nav-item-divider nav-item">
                                </li>
                                <li class="nav-item-btn text-center nav-item">
                                    <button class="btn-wide btn btn-primary btn-sm">
                                        Abrir Mensagens
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="widget-content-left  ml-3 header-user-info">
                    <div class="widget-heading">
                        <?php echo $_SESSION['nobre_usuario_nome']; ?>
                    </div>
                    <div class="widget-subheading">
                        <?php echo $_SESSION['nobre_usuario_titulo']; ?>
                    </div>
                </div>

            </div>
        </div>
    </div>
