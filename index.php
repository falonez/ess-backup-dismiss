if ($this->input->get('dismiss_notif')) {
			$dismiss_notif = $this->input->get('dismiss_notif'); // menangkap nilai 'status' dari URL
		} else {
			$dismiss_notif = true;
		}
		$dismiss_notif == 'true' ? $dismiss_notif = true : $dismiss_notif = false ;
		
		// Ambil bulan aktif berdasarkan cut off tanggal 10
		if (date('d') > 10) {
			// Jika tanggal sekarang lebih dari 10, maka bulan ini aktif
			$active_month = date('m');
			$active_year = date('Y');
		} else {
			// Jika tanggal sekarang kurang dari 10, ambil bulan lalu
			$active_month = date('m', strtotime('-1 month'));
			$active_year = date('Y', strtotime('-1 month'));
		}
		
		if ($dismiss_notif) {
			// Tentukan rentang tanggal dari tanggal 11 bulan aktif hingga tanggal 10 bulan berikutnya
			$start_date_cutoff = date("$active_year-$active_month-11");
			$next_month_cutoff = date('Y-m-10', strtotime('+1 month', strtotime("$active_year-$active_month-01")));
			
			// Fungsi untuk memfilter data berdasarkan rentang tanggal
			function filterDataByDateRange($data, $date_field, $start_date_cutoff, $next_month_cutoff) {
				$filtered_data = [];
				foreach ($data as $key => $value) {
					$date_value = $value[$date_field];
					if (strtotime($date_value) >= strtotime($start_date_cutoff) && strtotime($date_value) <= strtotime($next_month_cutoff)) {
						$filtered_data[] = $value;
					}
				}
				return $filtered_data;
			}
		
			// Penerapan filter pada setiap data
			if ($this->data['daftar_perizinan']) {
				$this->data['daftar_perizinan'] = filterDataByDateRange($this->data['daftar_perizinan'], 'dws_tanggal', $start_date_cutoff, $next_month_cutoff);
			}
			
			if ($this->data['daftar_kehadiran']) {
				$this->data['daftar_kehadiran'] = filterDataByDateRange($this->data['daftar_kehadiran'], 'dws_tanggal', $start_date_cutoff, $next_month_cutoff);
			}
			
			if ($this->data['daftar_lembur']) {
				$this->data['daftar_lembur'] = filterDataByDateRange($this->data['daftar_lembur'], 'tgl_dws', $start_date_cutoff, $next_month_cutoff);
			}
			
			if ($this->data['daftar_cuti']) {
				$daftar_cuti = $this->data['daftar_cuti'];
				$daftar_cuti_filtered = [];
				
				foreach ($daftar_cuti as $value) {
					$start_date = $value['start_date'];
					$end_date = $value['end_date'];
					
					// Jika start_date atau end_date berada dalam rentang yang diinginkan
					if ((strtotime($start_date) >= strtotime($start_date_cutoff) && strtotime($start_date) <= strtotime($next_month_cutoff)) ||
						(strtotime($end_date) >= strtotime($start_date_cutoff) && strtotime($end_date) <= strtotime($next_month_cutoff))) {
						$daftar_cuti_filtered[] = $value;
					}
				}
				
				$this->data['daftar_cuti'] = $daftar_cuti_filtered;
			}
			
			if ($this->data['daftar_makan_lembur']) {
				$this->data['daftar_makan_lembur'] = filterDataByDateRange($this->data['daftar_makan_lembur'], 'tanggal_pemesanan', $start_date_cutoff, $next_month_cutoff);
			}
			
			if ($this->data['daftar_konsumsi_rapat']) {
				$this->data['daftar_konsumsi_rapat'] = filterDataByDateRange($this->data['daftar_konsumsi_rapat'], 'tanggal_pemesanan', $start_date_cutoff, $next_month_cutoff);
			}
			
			if ($this->data['daftar_kendaraan']) {
				$this->data['daftar_kendaraan'] = filterDataByDateRange($this->data['daftar_kendaraan'], 'tanggal_berangkat', $start_date_cutoff, $next_month_cutoff);
			}
			
			if ($this->data['daftar_penilaian']) {
				$this->data['daftar_penilaian'] = filterDataByDateRange($this->data['daftar_penilaian'], 'tanggal_berangkat', $start_date_cutoff, $next_month_cutoff);
			}
			
			if ($this->data['all_pelaporan']) {
				$all_pelaporan = $this->data['all_pelaporan'];
				$all_pelaporan_filtered = [];
				
				foreach ($all_pelaporan as $array) {
					if (!empty($array['data'])) {
						$data = $array['data'];
						$data_filtered = filterDataByDateRange($data, 'created_at', $start_date_cutoff, $next_month_cutoff);
						
						// Hasil filter
						$array['data'] = $data_filtered;
						$all_pelaporan_filtered[] = $array;
					}
				}
		
				$this->data['all_pelaporan'] = $all_pelaporan_filtered;
			}
			
			if ($this->data['all_faskar']) {
				$all_faskar = $this->data['all_faskar'];
				$all_faskar_filtered = [];
				
				foreach ($all_faskar as $array) {
					if (!empty($array['data'])) {
						$data = $array['data'];
						$data_filtered = filterDataByDateRange($data, 'submit_date', $start_date_cutoff, $next_month_cutoff);
						
						// Hasil filter
						$array['data'] = $data_filtered;
						$all_faskar_filtered[] = $array;
					}
				}
		
				$this->data['all_faskar'] = $all_faskar_filtered;
			}
		}
