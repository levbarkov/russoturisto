config. FollowScroll = false
var fscrl = new tt_Extension();

// Implement extension eventhandlers on which our extension should react
fscrl.OnShow = function()
{
	if(tt_aV[FOLLOWSCROLL])
	{
		// Permit FOLLOWSCROLL only if the tooltip is sticky
		if(tt_aV[STICKY])
		{
			var x = tt_x - tt_GetScrollX(), y = tt_y - tt_GetScrollY();

			if(tt_ie)
			{
				fscrl.MoveOnScrl.offX = x;
				fscrl.MoveOnScrl.offY = y;
				fscrl.AddRemEvtFncs(tt_AddEvtFnc);
			}
			else
			{
				tt_SetTipPos(x, y);
				tt_aElt[0].style.position = "fixed";
			}
			return true;
		}
		tt_aV[FOLLOWSCROLL] = false;
	}
	return false;
};
fscrl.OnHide = function()
{
	if(tt_aV[FOLLOWSCROLL])
	{
		if(tt_ie)
			fscrl.AddRemEvtFncs(tt_RemEvtFnc);
		else
			tt_aElt[0].style.position = "absolute";
	}
};
// Helper functions (encapsulate in the class to avoid conflicts with other
// extensions)
fscrl.MoveOnScrl = function()
{
	tt_SetTipPos(fscrl.MoveOnScrl.offX + tt_GetScrollX(), fscrl.MoveOnScrl.offY + tt_GetScrollY());
};
fscrl.AddRemEvtFncs = function(PAddRem)
{
	PAddRem(window, "resize", fscrl.MoveOnScrl);
	PAddRem(window, "scroll", fscrl.MoveOnScrl);
};

