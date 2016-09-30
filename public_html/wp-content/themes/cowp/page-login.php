<?php get_header(); ?>

<?php while ( have_posts() ): the_post(); ?>
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<div class="normal-page">
					<div class="contato-form-wrapper">
						<?php if ( is_user_logged_in() ) 
						{ 
							$args = array(
								'redirect' => admin_url(), 
								'form_id' => 'form-login',
								'label_username' => __( 'Usuário' ),
								'label_password' => __( 'Senha' ),
								'label_remember' => __( 'Lembrar' ),
								'label_log_in' => __( 'Go' ),
								'remember' => true
								);
							$form = '
							<form name="' . $args['form_id'] . '" id="' . $args['form_id'] . '" action="' . esc_url( site_url( 'wp-login.php', 'login_post' ) ) . '" method="post">
								' . $login_form_top . '
								<p class="login-username">
									<label for="' . esc_attr( $args['id_username'] ) . '">' . esc_html( $args['label_username'] ) . '</label>
									<input type="text" name="log" id="' . esc_attr( $args['id_username'] ) . '" class="input" value="' . esc_attr( $args['value_username'] ) . '" size="20" />
								</p>
								<p class="login-password">
									<label for="' . esc_attr( $args['id_password'] ) . '">' . esc_html( $args['label_password'] ) . '</label>
									<input type="password" name="pwd" id="' . esc_attr( $args['id_password'] ) . '" class="input" value="" size="20" />
								</p>
								' . $login_form_middle . '
								' . ( $args['remember'] ? '<p class="login-remember"><label><input name="rememberme" type="checkbox" id="' . esc_attr( $args['id_remember'] ) . '" value="forever"' . ( $args['value_remember'] ? ' checked="checked"' : '' ) . ' /> ' . esc_html( $args['label_remember'] ) . '</label></p>' : '' ) . '
								<p class="login-submit">
									<input type="submit" name="wp-submit" id="' . esc_attr( $args['id_submit'] ) . '" class="button-primary" value="' . esc_attr( $args['label_log_in'] ) . '" />
									<input type="hidden" name="redirect_to" value="' . esc_url( $args['redirect'] ) . '" />
								</p>
								' . $login_form_bottom . '
								</form>';
								echo $form;
						} else { 
							wp_loginout( home_url() );
							echo " | ";
							wp_register('', ''); 
						}
						?>					</div>
					</div>
				</div>
			</div>
		</div>
	<?php endwhile; ?>

	<?php get_footer(); ?>
