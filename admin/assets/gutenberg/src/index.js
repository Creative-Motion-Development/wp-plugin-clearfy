/**
 * Gutenber autosave control. A simple solution for managing autosaves in gutenberg editor.
 * Previously, we simply turned off autosave using hooks, but in the editor,
 * you can’t do this in the gutenber.
 *
 * This widget for Gutenberg editor adds an icon, when clicked, you can select the autosave interval or full disable it.
 *
 * @author Webcraftic <wordpress.webraftic@gmail.com>
 * @copyright (c) 10.12.2018, Webcraftic
 * @version 1.0
 *
 * Credits:
 * This is not our development, we found excellent plugin and used these functions in our plugin. It is foolish to reinvent the wheel.
 * I hope in the future we will refine it better and add our ideas.
 * In the development of the code used by the author plugin: https://wordpress.org/plugins/disable-gutenberg-autosave/
 */

const NOT_TODAY = 99999;

const INTERVAL_OPTIONS = [
	{
		label: '10 seconds (default)',
		value: 10,
	},
	{
		label: '30 seconds',
		value: 30,
	},
	{
		label: '1 minute',
		value: 60,
	},
	{
		label: '5 minutes',
		value: 60 * 5,
	},
	{
		label: '10 minutes',
		value: 60 * 10,
	},
	{
		label: '30 minutes',
		value: 60 * 30,
	},
	{
		label: 'Disabled',
		value: NOT_TODAY,
	},
];

class ClearfyGutenbergAutosave extends React.Component {
	constructor(props) {
		super(props);

		this.state = {
			interval: 0,
			error: false,
		};

		this.apiGetInterval = this.apiGetInterval.bind(this);
		this.apiSetInterval = this.apiSetInterval.bind(this);
		this.editorUpdateInterval = this.editorUpdateInterval.bind(this);
	}

	apiGetInterval() {
		wp.apiFetch({path: '/clearfy-gutenberg-autosave/v1/interval'})
			.then(
				interval => {
					this.setState({
						interval,
						error: false,
					});
				},
				error => {
					this.setState({
						interval: NOT_TODAY,
						error: error.message,
					});
				}
			)
	}

	apiSetInterval() {
		if( this.state.error ) {
			return;
		}

		wp.apiFetch({
			path: '/clearfy-gutenberg-autosave/v1/interval?interval=' + parseInt(this.state.interval),
			method: 'POST',
		});
	}

	editorUpdateInterval() {
		this.props.updateEditorSettings(
			Object.assign(
				{},
				this.props.editorSettings,
				{autosaveInterval: parseInt(this.state.interval)}
			)
		);
	}

	componentDidMount() {
		this.apiGetInterval();
	}

	componentDidUpdate(prevProps, prevState) {
		if( !this.state.interval ) {
			return;
		}

		if( prevState.interval && prevState.inverval !== 0 && prevState.interval !== this.state.interval ) {
			this.apiSetInterval();
		}

		if( this.props.editorSettings.autosaveInterval && this.props.editorSettings.autosaveInterval !== this.state.interval ) {
			this.editorUpdateInterval();
		}
	}

	render() {
		return (
			<React.Fragment>
				<wp.editPost.PluginSidebarMoreMenuItem target='disable-gutenberg-autosave-sidebar'>
					{'Clearfy Gutenberg Autosave'}
				</wp.editPost.PluginSidebarMoreMenuItem>
				<wp.editPost.PluginSidebar name='disable-gutenberg-autosave-sidebar' title={'Autosave settings'}>
					<wp.components.PanelBody className='disable-gutenberg-autosave-settings'>
						{!this.state.interval && <p>{'Loading...'}</p>}
						{(!!this.state.interval && this.state.error) && (
							<React.Fragment>
								<h2 className='disable-gutenberg-autosave-header'>{'API error:'}</h2>
								<p className='disable-gutenberg-autosave-error'>{this.state.error}</p>
								<p>{'Autosave is disabled anyway, but you cannot set custom intervals.'}</p>
								<wp.components.Button
									className='button button-primary'
									onClick={() => {
										this.setState({
											interval: 0,
											error: false,
										});
										this.apiGetInterval();
									}}
								>
									{'Try again'}
								</wp.components.Button>
							</React.Fragment>
						)}
						{(!!this.state.interval && !this.state.error) && (
							<wp.components.RadioControl
								label={'Autosave interval'}
								options={INTERVAL_OPTIONS}
								selected={parseInt(this.state.interval)}
								onChange={value => this.setState({interval: parseInt(value)})}
							/>
						)}
					</wp.components.PanelBody>
				</wp.editPost.PluginSidebar>
			</React.Fragment>
		);
	}
}

wp.plugins.registerPlugin('clearfy-gutenberg-autosave', {
	icon: 'backup',
	render: wp.compose.compose([
		wp.data.withSelect(select => {
			return {
				editorSettings: select('core/editor').getEditorSettings(),
			};
		}),
		wp.data.withDispatch(dispatch => {
			return {
				updateEditorSettings: dispatch('core/editor').updateEditorSettings,
			};
		}),
	])(ClearfyGutenbergAutosave),
});
