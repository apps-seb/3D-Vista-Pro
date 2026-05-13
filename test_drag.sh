# To make it work, we need to capture the mouse events *before* the viewer's internal mechanics do.
# PhotoSphereViewer handles drag internally. The easiest way to block it is to use a capture phase listener on the container, OR to simply set `viewer.setOption('moveSpeed', 0)` when dragging starts, and restore it when it ends.
