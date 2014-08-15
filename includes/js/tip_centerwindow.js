config. CenterWindow = false
config. CenterAlways = false
var ctrwnd = new tt_Extension();
ctrwnd.OnLoadConfig = function()
{
	if(tt_aV[CENTERWINDOW])
	{
		// Permit CENTERWINDOW only if the tooltip is sticky
		if(tt_aV[STICKY])
		{
			if(tt_aV[CENTERALWAYS])
			{
				// IE doesn't support style.position "fixed"
				if(tt_ie)
					tt_AddEvtFnc(window, "scroll", Ctrwnd_DoCenter);
				else
					tt_aElt[0].style.position = "fixed";
				tt_AddEvtFnc(window, "resize", Ctrwnd_DoCenter);
			}
			return true;
		}
		tt_aV[CENTERWINDOW] = false;
	}
	return false;
};
// We react on the first OnMouseMove event to center the tip on that occasion
ctrwnd.OnMoveBefore = Ctrwnd_DoCenter;
ctrwnd.OnKill = function()
{
	if(tt_aV[CENTERWINDOW] && tt_aV[CENTERALWAYS])
	{
		tt_RemEvtFnc(window, "resize", Ctrwnd_DoCenter);
		if(tt_ie)
			tt_RemEvtFnc(window, "scroll", Ctrwnd_DoCenter);
		else
			tt_aElt[0].style.position = "absolute";
	}
	return false;
};
// Helper function
function Ctrwnd_DoCenter()
{
	if(tt_aV[CENTERWINDOW])
	{
		var x, y, dx, dy;

		// Here we use some functions and variables (tt_w, tt_h) which the
		// extension API of wz_tooltip.js provides for us
		if(tt_ie || !tt_aV[CENTERALWAYS])
		{
			dx = tt_GetScrollX();
			dy = tt_GetScrollY();
		}
		else
		{
			dx = 0;
			dy = 0;
		}
		// Position the tip, offset from the center by OFFSETX and OFFSETY
		x = (tt_GetClientW() - tt_w) / 2 + dx + tt_aV[OFFSETX];
		y = (tt_GetClientH() - tt_h) / 2 + dy + tt_aV[OFFSETY];
		tt_SetTipPos(x, y);
		return true;
	}
	return false;
}
