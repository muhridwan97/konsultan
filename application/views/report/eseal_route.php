<div class="form-horizontal form-view">
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Eseal Tracking</h3>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-4">No Safe Conduct</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <a href="<?= site_url('safe-conduct/view/' . $safeConduct['id']) ?>">
                                    <?= $safeConduct['no_safe_conduct'] ?>
                                </a>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Status Tracking</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= $safeConduct['status_tracking'] ?>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-4">E-seal</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= if_empty($safeConduct['no_eseal'], '-') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Device</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= if_empty($safeConduct['device_name'], '-') ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Eseal Route</h3>
        <div class="pull-right">
            <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=CONTAINER" class="btn btn-success">
                Export Excel
            </a>
        </div>
    </div>
    <div class="box-body">
        <table class="table table-bordered table-striped responsive">
            <thead>
            <tr>
                <th>No</th>
                <th>Longitude</th>
                <th>Latitude</th>
                <th>Distance</th>
                <th>Address</th>
                <th>Time</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($routes as $index => $route): ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td><?= numerical($route['longitude'], 8, true) ?></td>
                    <td><?= numerical($route['latitude'], 8, true) ?></td>
                    <td><?= numerical($route['distance'], 3, true) ?> <?= $route['distance_unit'] ?></td>
                    <td>
                        <a href="https://www.google.com/maps?q=<?= $route['latitude'] ?>,<?= $route['longitude'] ?>" target="_blank">
                            <?= $route['address'] ?>
                        </a>
                    </td>
                    <td><?= $route['time'] ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Map Route</h3>
    </div>
    <div class="box-body">
        <div id="map" style="height: 500px; width: 100%"></div>
    </div>
</div>

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDg4esaS2P9dXK7ApOBTdXcnfBy2heCKhw"></script>
<script>
    function initialize() {
        if ($('#map').length > 0) {
            var locations = <?= json_encode($routes) ?>;

            window.map = new google.maps.Map(document.getElementById('map'), {
                mapTypeId: google.maps.MapTypeId.ROADMAP,
                controlSize: 26,
            });

            var flightPlanCoordinates = [];
            var infowindow = new google.maps.InfoWindow();
            var bounds = new google.maps.LatLngBounds();

            for (var i = 0; i < locations.length; i++) {
                var marker = new google.maps.Marker({
                    position: new google.maps.LatLng(locations[i].latitude, locations[i].longitude),
                    map: (i === 0 || i === locations.length - 1) ? map : null // set first and last marker only
                });
                flightPlanCoordinates.push(marker.getPosition());
                bounds.extend(marker.position);

                google.maps.event.addListener(marker, 'click', (function (marker, i) {
                    return function () {
                        infowindow.setContent('<b>Route ' + (i === 0 ? 'start: ' : (i === locations.length - 1 ? 'end: ' : '')) +locations[i].time + '</b><br>' + locations[i].address);
                        infowindow.open(map, marker);
                    }
                })(marker, i));
            }

            map.fitBounds(bounds);

            var flightPath = new google.maps.Polyline({
                map: map,
                path: flightPlanCoordinates,
                strokeColor: "#ba151f",
                strokeOpacity: 0.7,
                strokeWeight: 5
            });
        }
    }
    google.maps.event.addDomListener(window, 'load', initialize);
</script>
